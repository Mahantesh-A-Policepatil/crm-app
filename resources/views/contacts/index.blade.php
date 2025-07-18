@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Contact List</h2>

        <div class="mb-3 d-flex align-items-center gap-3 flex-wrap">
            <a href="{{ route('contacts.create') }}" class="btn btn-success">+ Add Contact</a>
            <button id="mergeBtn" class="btn btn-warning" disabled>Merge Selected</button>
            <span id="merge-note" class="text-danger small">
                <i class="bi bi-info-circle me-1"></i>
                Please note: Merge Selected button is currently disabled. Select at least 2 contacts to enable it.
            </span>
        </div>

        <table class="table table-striped table-hover shadow rounded" id="contacts-table">
            <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
        </table>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="deleteForm" method="POST">
                @csrf @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">Are you sure you want to delete this contact?</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Merge Modal -->
    <div class="modal fade" id="mergeModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="mergeForm">@csrf
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">Select Master Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="mergeModalBody"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Merge</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let selectedContactIds = [];

        $(document).ready(function () {
            const table = $('#contacts-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("contacts.index") }}',
                columns: [
                    {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'phone', name: 'phone'},
                    {data: 'gender', name: 'gender'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                drawCallback: attachCheckboxListeners
            });

            $('#select-all').on('change', function () {
                $('.merge-checkbox').prop('checked', this.checked).trigger('change');
            });

            function attachCheckboxListeners() {
                $('.merge-checkbox').on('change', function () {
                    selectedContactIds = $('.merge-checkbox:checked').map(function () {
                        return $(this).val();
                    }).get();

                    const enabled = selectedContactIds.length >= 2;
                    $('#mergeBtn').prop('disabled', !enabled);
                    $('#merge-note')
                        .toggleClass('text-danger', !enabled)
                        .toggleClass('text-success', enabled)
                        .html(enabled
                            ? '<i class="bi bi-check-circle me-1"></i>You can now click Merge Selected.'
                            : '<i class="bi bi-info-circle me-1"></i>Please note: Merge Selected button is currently disabled. Select at least 2 contacts to enable it.');

                });
            }

            $('#mergeBtn').on('click', function () {
                const modalBody = $('#mergeModalBody');
                modalBody.empty();
                selectedContactIds.forEach((id) => {
                    const name = $(`#contact-name-${id}`).text();
                    modalBody.append(`
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="master_id" value="${id}" id="master-${id}">
                            <label class="form-check-label" for="master-${id}">${name}</label>
                        </div>
                    `);
                });

                new bootstrap.Modal(document.getElementById('mergeModal')).show();
            });

            $('#mergeForm').on('submit', function (e) {
                e.preventDefault();

                const master_id = $('input[name="master_id"]:checked').val();
                const secondary_ids = selectedContactIds.filter(id => id !== master_id);

                if (!master_id || !secondary_ids) {
                    Swal.fire('Error', 'Please select a master contact.', 'error');
                    return;
                }

                $.post('{{ route("contacts.merge") }}', {
                    _token: '{{ csrf_token() }}',
                    master_id,
                    secondary_ids
                }).done(function () {
                    Swal.fire('Merged!', 'Contacts have been merged.', 'success').then(() => {
                        bootstrap.Modal.getInstance(document.getElementById('mergeModal'))?.hide();
                        selectedContactIds = [];
                        $('#mergeBtn').prop('disabled', true);
                        $('#select-all').prop('checked', false);
                        table.ajax.reload();
                    });
                }).fail(function () {
                    Swal.fire('Error', 'Merge failed. Please try again.', 'error');
                });
            });

            window.confirmDelete = function (id) {
                $('#deleteForm').attr('action', `/contacts/${id}`);
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            };
        });
    </script>
@endpush
