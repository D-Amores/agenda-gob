@extends ('layouts.app')

@section('title') Audiencia @endsection
@section('content')

   <div class="col-12 mb-4">
    <small class="text-light fw-semibold">Validation</small>
    <div id="wizard-validation" class="bs-stepper mt-2 linear">
      <div class="bs-stepper-header">
        <div class="step active" data-target="#account-details-validation">
          <button type="button" class="step-trigger" aria-selected="true">
            <span class="bs-stepper-circle">1</span>
            <span class="bs-stepper-label mt-1">
              <span class="bs-stepper-title">Account Details</span>
              <span class="bs-stepper-subtitle">Setup Account Details</span>
            </span>
          </button>
        </div>
        <div class="line">
          <i class="bx bx-chevron-right"></i>
        </div>
        <div class="step" data-target="#personal-info-validation">
          <button type="button" class="step-trigger" aria-selected="false" disabled="disabled">
            <span class="bs-stepper-circle">2</span>
            <span class="bs-stepper-label mt-1">
              <span class="bs-stepper-title">Personal Info</span>
              <span class="bs-stepper-subtitle">Add personal info</span>
            </span>
          </button>
        </div>
        <div class="line">
          <i class="bx bx-chevron-right"></i>
        </div>
        <div class="step" data-target="#social-links-validation">
          <button type="button" class="step-trigger" aria-selected="false" disabled="disabled">
            <span class="bs-stepper-circle">3</span>
            <span class="bs-stepper-label mt-1">
              <span class="bs-stepper-title">Social Links</span>
              <span class="bs-stepper-subtitle">Add social links</span>
            </span>
          </button>
        </div>
      </div>
      <div class="bs-stepper-content">
        <form id="wizard-validation-form" onsubmit="return false">
          <!-- Account Details -->
          <div id="account-details-validation" class="content active dstepper-block fv-plugins-bootstrap5 fv-plugins-framework">
            <div class="content-header mb-3">
              <h6 class="mb-0">Account Details</h6>
              <small>Enter Your Account Details.</small>
            </div>
            <div class="row g-3">
              <div class="col-sm-6 fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
                <label class="form-label" for="formValidationUsername">Username</label>
                <input type="text" name="formValidationUsername" id="formValidationUsername" class="form-control is-invalid" placeholder="johndoe">
              <div class="fv-plugins-message-container invalid-feedback"><div data-field="formValidationUsername" data-validator="notEmpty">The name is required</div></div></div>
              <div class="col-sm-6 fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
                <label class="form-label" for="formValidationEmail">Email</label>
                <input type="email" name="formValidationEmail" id="formValidationEmail" class="form-control is-invalid" placeholder="john.doe@email.com" aria-label="john.doe">
              <div class="fv-plugins-message-container invalid-feedback"><div data-field="formValidationEmail" data-validator="notEmpty">The Email is required</div></div></div>
              <div class="col-sm-6 form-password-toggle fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
                <label class="form-label" for="formValidationPass">Password</label>
                <div class="input-group input-group-merge has-validation">
                  <input type="password" id="formValidationPass" name="formValidationPass" class="form-control is-invalid" placeholder="············" aria-describedby="formValidationPass2">
                  <span class="input-group-text cursor-pointer" id="formValidationPass2"><i class="bx bx-hide"></i></span>
                </div><div class="fv-plugins-message-container invalid-feedback"><div data-field="formValidationPass" data-validator="notEmpty">The password is required</div></div>
              </div>
              <div class="col-sm-6 form-password-toggle fv-plugins-icon-container fv-plugins-bootstrap5-row-invalid">
                <label class="form-label" for="formValidationConfirmPass">Confirm Password</label>
                <div class="input-group input-group-merge has-validation">
                  <input type="password" id="formValidationConfirmPass" name="formValidationConfirmPass" class="form-control is-invalid" placeholder="············" aria-describedby="formValidationConfirmPass2">
                  <span class="input-group-text cursor-pointer" id="formValidationConfirmPass2"><i class="bx bx-hide"></i></span>
                </div><div class="fv-plugins-message-container invalid-feedback"><div data-field="formValidationConfirmPass" data-validator="notEmpty">The Confirm Password is required</div></div>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-label-secondary btn-prev" disabled="">
                  <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next">
                  <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                  <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                </button>
              </div>
            </div>
          <div></div></div>
          <!-- Personal Info -->
          <div id="personal-info-validation" class="content fv-plugins-bootstrap5 fv-plugins-framework">
            <div class="content-header mb-3">
              <h6 class="mb-0">Personal Info</h6>
              <small>Enter Your Personal Info.</small>
            </div>
            <div class="row g-3">
              <div class="col-sm-6 fv-plugins-icon-container">
                <label class="form-label" for="formValidationFirstName">First Name</label>
                <input type="text" id="formValidationFirstName" name="formValidationFirstName" class="form-control" placeholder="John">
              <div class="fv-plugins-message-container invalid-feedback"></div></div>
              <div class="col-sm-6 fv-plugins-icon-container">
                <label class="form-label" for="formValidationLastName">Last Name</label>
                <input type="text" id="formValidationLastName" name="formValidationLastName" class="form-control" placeholder="Doe">
              <div class="fv-plugins-message-container invalid-feedback"></div></div>
              <div class="col-sm-6 fv-plugins-icon-container">
                <label class="form-label" for="formValidationCountry">Country</label>
                <div class="position-relative"><div class="position-relative"><select class="select2 select2-hidden-accessible" id="formValidationCountry" name="formValidationCountry" tabindex="-1" aria-hidden="true" data-select2-id="formValidationCountry">
                  <option label=" " data-select2-id="27"></option>
                  <option>UK</option>
                  <option>USA</option>
                  <option>Spain</option>
                  <option>France</option>
                  <option>Italy</option>
                  <option>Australia</option>
                </select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="26" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-formValidationCountry-container"><span class="select2-selection__rendered" id="select2-formValidationCountry-container" role="textbox" aria-readonly="true"><span class="select2-selection__placeholder">Select value</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span></div></div>
              <div class="fv-plugins-message-container invalid-feedback"></div></div>
              <div class="col-sm-6 fv-plugins-icon-container">
                <label class="form-label" for="formValidationLanguage">Language</label>
                <div class="dropdown bootstrap-select show-tick w-auto"><select class="selectpicker w-auto" id="formValidationLanguage" data-style="btn-transparent" data-icon-base="bx" data-tick-icon="bx-check text-white" name="formValidationLanguage" multiple="">
                  <option>English</option>
                  <option>French</option>
                  <option>Spanish</option>
                </select><button type="button" tabindex="-1" class="btn dropdown-toggle bs-placeholder btn-transparent" data-bs-toggle="dropdown" role="combobox" aria-owns="bs-select-2" aria-haspopup="listbox" aria-expanded="false" title="Nothing selected" data-id="formValidationLanguage"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">Nothing selected</div></div> </div></button><div class="dropdown-menu "><div class="inner show" role="listbox" id="bs-select-2" tabindex="-1" aria-multiselectable="true"><ul class="dropdown-menu inner show" role="presentation"></ul></div></div></div>
              <div class="fv-plugins-message-container invalid-feedback"></div></div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-primary btn-prev">
                  <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-primary btn-next">
                  <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                  <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                </button>
              </div>
            </div>
          <div></div></div>
          <!-- Social Links -->
          <div id="social-links-validation" class="content fv-plugins-bootstrap5 fv-plugins-framework">
            <div class="content-header mb-3">
              <h6 class="mb-0">Social Links</h6>
              <small>Enter Your Social Links.</small>
            </div>
            <div class="row g-3">
              <div class="col-sm-6 fv-plugins-icon-container">
                <label class="form-label" for="formValidationTwitter">Twitter</label>
                <input type="text" name="formValidationTwitter" id="formValidationTwitter" class="form-control" placeholder="https://twitter.com/abc">
              <div class="fv-plugins-message-container invalid-feedback"></div></div>
              <div class="col-sm-6 fv-plugins-icon-container">
                <label class="form-label" for="formValidationFacebook">Facebook</label>
                <input type="text" name="formValidationFacebook" id="formValidationFacebook" class="form-control" placeholder="https://facebook.com/abc">
              <div class="fv-plugins-message-container invalid-feedback"></div></div>
              <div class="col-sm-6 fv-plugins-icon-container">
                <label class="form-label" for="formValidationGoogle">Google+</label>
                <input type="text" name="formValidationGoogle" id="formValidationGoogle" class="form-control" placeholder="https://plus.google.com/abc">
              <div class="fv-plugins-message-container invalid-feedback"></div></div>
              <div class="col-sm-6 fv-plugins-icon-container">
                <label class="form-label" for="formValidationLinkedIn">LinkedIn</label>
                <input type="text" name="formValidationLinkedIn" id="formValidationLinkedIn" class="form-control" placeholder="https://linkedin.com/abc">
              <div class="fv-plugins-message-container invalid-feedback"></div></div>
              <div class="col-12 d-flex justify-content-between">
                <button class="btn btn-primary btn-prev">
                  <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button class="btn btn-success btn-next btn-submit">Submit</button>
              </div>
            </div>
          <div></div></div>
        </form>
      </div>
    </div>
  </div>

@endsection