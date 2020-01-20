<!DOCTYPE html>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<style type="text/css">    
    @media screen {
        @font-face {
          font-family: 'Lato';
          font-style: normal;
          font-weight: 400;
          src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v11/qIIYRU-oROkIk8vfvxw6QvesZW2xOQ-xsNqO47m55DA.woff) format('woff');
        }
        
        @font-face {
          font-family: 'Lato';
          font-style: normal;
          font-weight: 700;
          src: local('Lato Bold'), local('Lato-Bold'), url(https://fonts.gstatic.com/s/lato/v11/qdgUG4U09HnJwhYI-uK18wLUuEpTyoUstqEm5AMlJo4.woff) format('woff');
        }
        
        @font-face {
          font-family: 'Lato';
          font-style: italic;
          font-weight: 400;
          src: local('Lato Italic'), local('Lato-Italic'), url(https://fonts.gstatic.com/s/lato/v11/RYyZNoeFgb0l7W3Vu1aSWOvvDin1pK8aKteLpeZ5c0A.woff) format('woff');
        }
        
        @font-face {
          font-family: 'Lato';
          font-style: italic;
          font-weight: 700;
          src: local('Lato Bold Italic'), local('Lato-BoldItalic'), url(https://fonts.gstatic.com/s/lato/v11/HkF_qI1x_noxlxhrhMQYELO3LdcAZYWl9Si6vvxL-qU.woff) format('woff');
        }
    }
    
    /* CLIENT-SPECIFIC STYLES */
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; }

    /* RESET STYLES */
    img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
    table { border-collapse: collapse !important; }
    body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }

    /* iOS BLUE LINKS */
    a[x-apple-data-detectors] {
        color: inherit !important;
        text-decoration: none !important;
        font-size: inherit !important;
        font-family: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
    }
    
    /* MOBILE STYLES */
    @media screen and (max-width:600px){
        h1 {
            font-size: 32px !important;
            line-height: 32px !important;
        }
    }

    /* ANDROID CENTER FIX */
    div[style*="margin: 16px 0;"] { margin: 0 !important; }
</style>
</head>

<body style="background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;">
<!-- FORMATO DE CORREO, PARA RECIBIR CODIGO DE RECUPERACION DE CONTRASEÑA -->

<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td bgcolor="#FFA73B" align="center">          
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;" >
                <tr>
                    <td align="center" valign="top" style="padding: 40px 10px 40px 10px;">
                        <div href="http://www.google.com" target="_blank">
                            <div bgcolor="#ffffff" align="center" style="color: #FFFFFF; font-family: Arial, sans-serif; font-size: 30px;" >
                              <p style="margin: 0;">Tatan</p>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>          
        </td>
    </tr>
    <tr>
        <td bgcolor="#FFA73B" align="center" style="padding: 0px 10px 0px 10px;">           
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;" >
                <tr>
                    <td bgcolor="#ffffff" align="center" style="padding: 20px 30px 40px 30px; color: #0D2C55; font-family: Arial, sans-serif; font-size: 18px;" >
                      <p style="margin: 0;">Estimado/a {{ $nombre }}.</p>
                    </td>
                </tr>
            </table>          
        </td>
    </tr>
    <tr>
        <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">            
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">             
             <tr>
                <td bgcolor="#ffffff" align="center" style="padding: 10px 20px 30px 20px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;" >
                  <p style="margin: 0;">Código de Recuperación.</p>
                </td>
              </tr>

              <tr>
                <td bgcolor="#ffffff" align="center" style="padding: 10px 20px 30px 20px; color: #0D2C55; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;" >
                  <p style="margin: 0;">{{ $codigo }}</p>
                </td>
              </tr>
              <tr>
                <td bgcolor="#ffffff" align="center" style="padding: 10px 20px 30px 20px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;" >
                  <p style="margin: 0;">Se ha solicitado un código de recuperación de contraseña, si usted no realizo esta solicitud, puede ignorar este mensaje y su contraseña no se vera afectada.</p>
                </td>
              </tr>   
            </table>           
        </td>
    </tr>
    <tr>
        <td bgcolor="#f4f4f4" align="center" style="padding: 30px 10px 0px 10px;">           
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;" >
                <tr>
                  <td bgcolor="#FFECD1" align="center" style="padding: 30px 30px 30px 30px; border-radius: 4px 4px 4px 4px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;" >
                    <h2 style="font-size: 20px; font-weight: 400; color: #111111; margin: 0;">Necesita ayuda?</h2>
                    <p style="margin: 0;"><a href="http://www.google.com" target="_blank" style="color: #1976d2;">Contactanos</a></p>
                  </td>
                </tr>
            </table>            
        </td>  
    </tr>
    <tr>
        <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">           
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;" >
              <tr>
                <td bgcolor="#f4f4f4" align="left" style="padding: 30px 30px 30px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 18px;" >
                  <p style="margin: 0;">
                    <a href="http://google.com" target="_blank" style="color: #111111; font-weight: 700;">Términos y Condiciones</a> -
                    <a href="http://google.com" target="_blank" style="color: #111111; font-weight: 700;">Preguntas Frecuentes</a>                     
                  </p>
                </td>
              </tr>          
            </table>           
        </td>
    </tr>
</table>
    
</body>
</html>