<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta {{ $activo->modelo->modelo ?? 'Activo' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
            /* Fondo gris para pantalla */
        }

        .etiqueta {
            background: white;
            width: 300px;
            /* Tamaño etiqueta estándar */
            padding: 20px;
            border: 2px solid black;
            text-align: center;
            border-radius: 10px;
        }

        #qrcode {
            display: flex;
            justify-content: center;
            margin: 15px 0;
        }

        .tipo {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .titulo {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .subtitulo {
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
        }

        .uuid {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #333;
            margin-top: 10px;
        }

        /* ESTILOS PARA IMPRESIÓN */
        @media print {
            body {
                background: white;
                height: auto;
            }

            .etiqueta {
                border: none;
                width: 100%;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body>

    <div class="etiqueta">
        <div class="tipo">{{ $activo->tipo->tipo ?? 'TIPO' }}</div>
        <div class="titulo">{{ $activo->modelo->modelo ?? 'ACTIVO' }}</div>
        <div class="subtitulo">{{ $activo->modelo->marca->marca ?? '' }}</div>

        <div id="qrcode"></div>

        <div class="uuid">ID: {{ $activo->uuid }}</div>
    </div>

    <div class="no-print" style="margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Imprimir Etiqueta</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Cerrar</button>
    </div>

    <script>
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: "{{ $activo->uuid }}",
            width: 150,
            height: 150,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        /*window.onload = function() {
            window.print();
        }*/
    </script>

</body>

</html>