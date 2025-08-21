const today = new Date();

flatpickr("#formValidationFecha", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d-m-Y",
    defaultDate: today,
    minDate: today,
    altInputClass: "form-control flatpickr-input"
});
