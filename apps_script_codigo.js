const SHEET_ID = '15Cms7ZkLMjUSD-g8njILqayVU3BlOlDfsFajRVMT-OA';

/** Llamada GET — viene del navegador del usuario (fetch no-cors) */
function doGet(e) {
  return procesarRegistro(e.parameter);
}

/** Llamada POST — viene del servidor PHP (respaldo) */
function doPost(e) {
  return procesarRegistro(e.parameter);
}

/** Solo registra en el Sheet. El email lo gestiona PHP SMTP. */
function procesarRegistro(params) {
  try {
    var nombre   = params.nombre   || '';
    var correo   = params.correo   || '';
    var telefono = params.telefono || '';
    var dni      = params.dni      || '';
    var nivel    = params.nivel    || '';
    var fecha    = Utilities.formatDate(new Date(), 'America/Lima', 'dd/MM/yyyy HH:mm:ss');

    var ss    = SpreadsheetApp.openById(SHEET_ID);
    var sheet = ss.getSheets()[0];

    // Crear encabezados si la hoja esta vacia
    if (sheet.getLastRow() === 0) {
      sheet.appendRow(['Fecha', 'Nombre', 'Correo', 'Telefono', 'DNI', 'Nivel']);
      sheet.getRange(1, 1, 1, 6)
           .setFontWeight('bold')
           .setBackground('#1a56db')
           .setFontColor('#ffffff');
    }

    // Registrar nueva fila
    sheet.appendRow([fecha, nombre, correo, telefono, dni, nivel]);

    return ContentService
      .createTextOutput(JSON.stringify({ status: 'success' }))
      .setMimeType(ContentService.MimeType.JSON);

  } catch (err) {
    return ContentService
      .createTextOutput(JSON.stringify({ status: 'error', message: err.toString() }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}
