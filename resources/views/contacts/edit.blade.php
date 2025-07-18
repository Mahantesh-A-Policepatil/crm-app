@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">✏️ Edit Contact</h2>

        <div id="alertBox"></div>

        <form id="contactEditForm" enctype="multipart/form-data" class="card p-4 shadow rounded">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ $contact->name }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ $contact->email }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="{{ $contact->phone }}">
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Gender</label>
                @php $gender = $contact->gender; @endphp
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="male"
                           id="genderMale" {{ $gender === 'male' ? 'checked' : '' }}>
                    <label class="form-check-label" for="genderMale">Male</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="female"
                           id="genderFemale" {{ $gender === 'female' ? 'checked' : '' }}>
                    <label class="form-check-label" for="genderFemale">Female</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="other"
                           id="genderOther" {{ $gender === 'other' ? 'checked' : '' }}>
                    <label class="form-check-label" for="genderOther">Other</label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Profile Image</label>
                @if ($contact->profile_image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $contact->profile_image) }}" alt="Profile Image"
                             class="img-thumbnail" width="100">
                    </div>
                @endif
                <input type="file" name="profile_image" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Additional File</label>
                @if ($contact->additional_file)
                    <div class="mb-2">
                        <a href="{{ asset('storage/' . $contact->additional_file) }}" target="_blank"
                           class="btn btn-sm btn-outline-secondary">View Existing File</a>
                    </div>
                @endif
                <input type="file" name="additional_file" class="form-control">
            </div>

            <div class="text-end">
                <button type="submit" id="submitBtn" class="btn btn-success">
                    <span class="spinner-border spinner-border-sm d-none" id="spinner"></span>
                    Update Contact
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $('#contactEditForm').submit(function (e) {
            e.preventDefault();

            $('#spinner').removeClass('d-none');
            $('#submitBtn').attr('disabled', true);
            $('#alertBox').html('');

            let form = $(this)[0];
            let formData = new FormData(form);

            // Laravel expects method override for PUT requests
            formData.append('_method', 'PUT');

            let contactId = $('#contactId').val(); // get the contact ID

            $.ajax({
                url: "{{ route('contacts.update', $contact->id) }}", // or use route helper via Blade
                type: 'POST', // Laravel will treat this as PUT because of _method
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: response.message || 'Contact updated successfully.',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        setTimeout(function () {
                            window.location.href = "{{ route('contacts.index') }}";
                        }, 1600);
                    }
                },
                error: function (xhr) {
                    let message = 'Update failed!';
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
