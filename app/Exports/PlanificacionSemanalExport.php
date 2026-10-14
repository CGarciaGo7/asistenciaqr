<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PlanificacionSemanalExport implements FromArray, WithEvents, WithColumnWidths
{
    private array $datos;
    private array $colores = [];

    public function __construct(array $datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        $sucursal = $this->datos['sucursal'];
        $dias = $this->datos['dias'];
        $turnos = $this->datos['turnos'];

        $filas = [
            ['PLANIFICACIÓN SEMANAL DE TURNOS', '', '', '', '', '', '', '', ''],
            [
                'Sucursal: ' . $sucursal->nombre .
                ' | Semana: ' .
                $this->datos['fechaInicio']->format('d/m/Y') .
                ' al ' .
                $this->datos['fechaFin']->format('d/m/Y'),
                '', '', '', '', '', '', '', '',
            ],
            array_merge(
                ['TRABAJADOR', 'CARGO'],
                $dias->map(fn ($dia) =>
                    ucfirst(Carbon::parse($dia)->locale('es')->isoFormat('ddd DD/MM'))
                )->all()
            ),
        ];

        $registros = $turnos->flatten(1);

        $trabajadores = $registros
            ->filter(fn ($turno) => $turno->trabajador)
            ->pluck('trabajador')
            ->unique('id')
            ->sortBy('primer_apellido')
            ->values();

        foreach ($trabajadores as $trabajador) {
            $nombre = trim(implode(' ', array_filter([
                $trabajador->primer_nombre,
                $trabajador->segundo_nombre,
                $trabajador->primer_apellido,
                $trabajador->segundo_apellido,
            ])));

            $cargo = $trabajador->cargo->nombre
                ?? 'Cargo no especificado';

            $fila = [$nombre, $cargo];
            $numeroFila = count($filas) + 1;

            foreach ($dias as $indice => $dia) {
                $fecha = Carbon::parse($dia)->toDateString();

                $registro = $registros->first(function ($turno) use (
                    $trabajador,
                    $fecha
                ) {
                    return (int) $turno->trabajador_id === (int) $trabajador->id
                        && Carbon::parse($turno->fecha)->toDateString() === $fecha;
                });

                if (!$registro) {
                    $fila[] = 'Sin asignación';
                    $this->colores[$numeroFila][$indice + 3] = 'F1F5F9';
                    continue;
                }

                if ($registro->es_descanso) {
                    $fila[] = 'DESCANSO';
                    $this->colores[$numeroFila][$indice + 3] = 'E2E8F0';
                    continue;
                }

                if (!$registro->horario) {
                    $fila[] = 'Horario no disponible';
                    $this->colores[$numeroFila][$indice + 3] = 'FEE2E2';
                    continue;
                }

                $horario = $registro->horario;

                $entrada = Carbon::parse($horario->hora_ingreso);
                $salida = Carbon::parse($horario->hora_salida);

                // Primer bloque del turno
                $textoHorario = $entrada->format('H:i') . ' - ' .
                    $salida->format('H:i');

                // Segundo bloque, únicamente si el turno es PARTIDO
                if (
                    strtoupper(trim($horario->tipo_turno ?? '')) === 'PARTIDO'
                    && !empty($horario->hora_ingreso2)
                    && !empty($horario->hora_salida2)
                ) {
                    $entrada2 = Carbon::parse($horario->hora_ingreso2);
                    $salida2 = Carbon::parse($horario->hora_salida2);

                    $textoHorario .= "\n" .
                        $entrada2->format('H:i') . ' - ' .
                        $salida2->format('H:i');
                }

                // Mostrar el nombre del horario y ambos bloques cuando corresponda
                $fila[] = $horario->nombre . "\n" . $textoHorario;

                $minutos = $entrada->hour * 60 + $entrada->minute;

                if (
                    $salida->diffInMinutes($entrada) >= 600
                    && $entrada->hour >= 7
                    && $entrada->hour <= 9
                ) {
                    $color = 'F3E8FF';
                } elseif ($minutos >= 840) {
                    $color = 'FFEDD5';
                } elseif ($minutos >= 600) {
                    $color = 'DCFCE7';
                } else {
                    $color = 'DBEAFE';
                }

                $this->colores[$numeroFila][$indice + 3] = $color;
            }

            $filas[] = $fila;
        }

        return $filas;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 23,
            'C' => 18,
            'D' => 18,
            'E' => 18,
            'F' => 18,
            'G' => 18,
            'H' => 18,
            'I' => 18,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $ultimaFila = $sheet->getHighestRow();

                $sheet->mergeCells('A1:I1');
                $sheet->mergeCells('A2:I2');

                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(25);
                $sheet->getRowDimension(3)->setRowHeight(32);

                $sheet->getStyle('A1:I1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '075985'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A2:I2')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '334155']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E0F2FE'],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A3:I3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '087F9F'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                if ($ultimaFila >= 4) {
                    $sheet->getStyle("A4:I{$ultimaFila}")->applyFromArray([
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CBD5E1'],
                            ],
                        ],
                    ]);
                }

                foreach ($this->colores as $fila => $columnas) {
                    foreach ($columnas as $columna => $color) {
                        $letra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                            $columna
                        );

                        $sheet->getStyle("{$letra}{$fila}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $color],
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                                'wrapText' => true,
                            ],
                        ]);
                    }

                    $sheet->getRowDimension($fila)->setRowHeight(42);
                }

                $sheet->freezePane('C4');
            },
        ];
    }
}
