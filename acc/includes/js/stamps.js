// выбран ли тип штампа "другое"
function is_another_type(type) {
  return type === '36';
}

function change_type(type) {
  // Изменение типа штампа
  var side_require = $('#side_require');
  if (is_another_type(type)) {
    side_require.text('');
  } else {
    side_require.text('*');
  }
}

function is_integer(value) {
  return /^[0-9]+$/.test(value);
}
