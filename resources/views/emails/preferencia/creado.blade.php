@component('mail::message')
# Nueva preferencia de pago creada

### Detalles del pago:
- MONTO: {{$monto}}
- AÑO: {{$anio}}
- MES: {{$mes}}

Sigue el siguiente boton para completar tu pago:

@component('mail::button', ['url' => '{{$preferencia_url}}'])
CONTINUAR CON MERCADO PAGO
@endcomponent

Si no funciona el boton, copia el siguiente enlace:
[{{$preferencia_url}}]({{$preferencia_url}})

Saludos.
@endcomponent
