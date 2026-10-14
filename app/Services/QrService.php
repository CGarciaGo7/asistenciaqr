<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrService
{
    public function generar(string $codigo): string
    {
        $nombreArchivo = $codigo . '.svg';

        $ruta = storage_path(
            'app/public/trabajadores/qr/' . $nombreArchivo
        );

        QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($codigo, $ruta);

        return 'trabajadores/qr/' . $nombreArchivo;
    }
}