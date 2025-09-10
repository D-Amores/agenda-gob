# 📧 Configuración de Gmail para Emails de Verificación

## Paso 1: Activar verificación en 2 pasos (Si no la tienes)

1. Ve a [myaccount.google.com](https://myaccount.google.com)
2. Haz clic en **"Seguridad"** en el menú izquierdo
3. En la sección "Iniciar sesión en Google", busca **"Verificación en 2 pasos"**
4. Sigue las instrucciones para activarla (necesitarás tu teléfono)

## Paso 2: Generar contraseña de aplicación

1. Una vez activada la verificación en 2 pasos, regresa a **Seguridad**
2. Busca la sección **"Contraseñas de aplicaciones"** 
3. Haz clic en **"Contraseñas de aplicaciones"**
4. Es posible que te pida tu contraseña nuevamente
5. En "Seleccionar aplicación" elige **"Otra (nombre personalizado)"**
6. Escribe: **"Laravel Agenda Gob"**
7. Haz clic en **"Generar"**
8. **¡IMPORTANTE!** Copia la contraseña de 16 caracteres que aparece

## Paso 3: Actualizar archivo .env

Abre el archivo `.env` en tu proyecto y actualiza estas líneas:

```bash
MAIL_USERNAME=tu-email@gmail.com        # ← Tu email de Gmail
MAIL_PASSWORD=abcd efgh ijkl mnop        # ← La contraseña de 16 caracteres (con espacios)
```

## Paso 4: Limpiar caché y probar

```bash
php artisan config:clear
php artisan cache:clear
php artisan test:send-verification-email
```

## ⚠️ Notas importantes:

- **NO uses tu contraseña normal de Gmail**, solo la contraseña de aplicación
- La contraseña de aplicación tiene espacios, déjalos tal como están
- Si no ves "Contraseñas de aplicaciones", asegúrate de tener activada la verificación en 2 pasos
- La contraseña de aplicación solo se muestra una vez, guárdala bien

## 🔧 Solución de problemas:

Si no encuentras "Contraseñas de aplicaciones":
1. Asegúrate de tener verificación en 2 pasos activada
2. Ve directamente a: [security.google.com/settings/security/apppasswords](https://security.google.com/settings/security/apppasswords)
3. Si aún no aparece, tu cuenta podría tener activado el "Acceso de aplicaciones menos seguras" (desactívalo)

## 🎯 Resultado esperado:

Una vez configurado correctamente, cuando alguien se registre:
1. Recibirá un email real en su bandeja de entrada
2. Podrá hacer clic en el enlace de verificación
3. Su cuenta será verificada automáticamente
