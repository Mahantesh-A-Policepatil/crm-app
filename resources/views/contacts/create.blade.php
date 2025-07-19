@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">➕ Add New Contact</h2>

        <div id="alertBox"></div>

        <form id="contactForm" enctype="multipart/form-data" class="card p-4 shadow rounded">
            @csrf

            <div class="mb-3">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="someone@example.com">
            </div>

            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" placeholder="+91-9876543210">
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Gender</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="male" id="genderMale">
                    <label class="form-check-label" for="genderMale">Male</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="female" id="genderFemale">
                    <label class="form-check-label" for="genderFemale">Female</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="other" id="genderOther">
                    <label class="form-check-label" for="genderOther">Other</label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Profile Image</label>
                <input type="file" name="profile_image" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Additional File</label>
                <input type="file" name="additional_file" class="form-control">
            </div>

            <div class="text-end">
                <button type="submit" id="submitBtn" class="btn btn-primary">
                    <span class="spinner-border spinner-border-sm d-none" id="spinner"></span>
                    Save Contact
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $('#contactForm').submit(function (e) {
            e.preventDefault();

            $('#spinner').removeClass('d-none');
            $('#submitBtn').attr('disabled', true);
            $('#alertBox').html('');

            //let formData = new FormData(this);

            let form = $(this)[0]; // Get the actual form DOM element
            let formData = new FormData(form);

            $.ajax({
                url: "{{ route('contacts.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Contact created successfully.',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        // Redirect after short delay (after Swal closes)
                        setTimeout(function () {
                            window.location.href = "{{ route('contacts.index') }}";
                        }, 1600); // wait for Swal to disappear
                    }
                },
                error: function (xhr) {
                    let message = 'An error occurred!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: message
                    });
                }
            });

        });
    </script>
@endpush
