<x-master-layout>
    <main class="main-area">
        <div class="main-content">
            <div class="container-fluid">
                @include('partials._provider')

                <!-- Company Documents Section -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Company Requirements (Admin Approved)</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Requirement</th>
                                        <th>Document File</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $companyDocKeys = [
                                            'business_license' => 'Business License',
                                            'state_id' => 'State ID (Company)',
                                            'background_check' => 'Background Check',
                                            'liability_insurance' => 'Liability Insurance',
                                            'business_ein' => 'Business EIN',
                                        ];
                                    @endphp

                                    @foreach($companyDocKeys as $key => $label)
                                        @php
                                            $doc = $companyRequirements->get($key);
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong class="text-dark">{{ $label }}</strong>
                                                <br>
                                                <small class="text-muted"><code>{{ $key }}</code></small>
                                            </td>
                                            <td>
                                                @if($doc && $doc->file_url)
                                                    <a href="{{ $doc->file_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="ri-eye-line me-1"></i> Preview File
                                                    </a>
                                                @else
                                                    <span class="text-muted"><i class="ri-close-circle-line me-1"></i> Not Uploaded</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$doc)
                                                    <span class="badge bg-secondary">Not Uploaded</span>
                                                @elseif($doc->status === 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($doc->status === 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Pending Review</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $doc->remarks ?? '-' }}
                                            </td>
                                            <td>
                                                @if($doc)
                                                    <button type="button" class="btn btn-sm btn-success me-1" data-bs-toggle="modal" data-bs-target="#actionModal{{ $doc->id }}" onclick="setDocStatus('{{ $doc->id }}', 'approved')">
                                                        <i class="ri-check-line"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#actionModal{{ $doc->id }}" onclick="setDocStatus('{{ $doc->id }}', 'rejected')">
                                                        <i class="ri-close-line"></i> Reject
                                                    </button>

                                                    <!-- Modal for remarks and confirmation -->
                                                    <div class="modal fade" id="actionModal{{ $doc->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form action="{{ route('provider.requirement.status') }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="id" value="{{ $doc->id }}">
                                                                    <input type="hidden" name="status" id="modalStatus{{ $doc->id }}" value="approved">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Review: {{ $label }}</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Update verification status for <strong>{{ $providerdata->display_name }}</strong>'s {{ $label }}.</p>
                                                                        <div class="form-group mb-3">
                                                                            <label class="form-label">Status</label>
                                                                            <select class="form-control" name="status" id="selectStatus{{ $doc->id }}">
                                                                                <option value="approved" {{ $doc->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                                                <option value="rejected" {{ $doc->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                                                <option value="pending" {{ $doc->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group mb-3">
                                                                            <label class="form-label">Remarks / Feedback (Optional)</label>
                                                                            <textarea class="form-control" name="remarks" rows="3" placeholder="Enter remarks or reason if rejecting...">{{ $doc->remarks }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Handyman Staff Documents Section -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Handyman Staff Requirements (Provider Verified)</h4>
                        <small class="text-muted">Handyman staff documents are verified by the provider</small>
                    </div>
                    <div class="card-body">
                        @if($handymen->isEmpty())
                            <p class="text-muted text-center py-4">No handyman staff members added yet for this provider.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Handyman</th>
                                            <th>Document</th>
                                            <th>File</th>
                                            <th>Status</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $handymanDocKeys = [
                                                'state_id' => 'State ID',
                                                'background_check' => 'Background Check',
                                                'profile_photo' => 'Profile Photo',
                                                'certificate' => 'Certificate',
                                            ];
                                        @endphp
                                        @foreach($handymen as $handyman)
                                            @php
                                                $docs = $handyman->handymanRequirements->keyBy('key');
                                            @endphp
                                            @foreach($handymanDocKeys as $key => $label)
                                                @php $item = $docs->get($key); @endphp
                                                <tr>
                                                    @if($loop->first)
                                                        <td rowspan="4" class="fw-bold align-middle bg-light">
                                                            {{ $handyman->display_name ?? ($handyman->first_name . ' ' . $handyman->last_name) }}
                                                            <br>
                                                            <small class="text-muted">ID: #{{ $handyman->id }}</small>
                                                        </td>
                                                    @endif
                                                    <td>
                                                        {{ $label }}
                                                        <br>
                                                        <small class="text-muted"><code>{{ $key }}</code></small>
                                                    </td>
                                                    <td>
                                                        @if($item && $item->file_url)
                                                            <a href="{{ $item->file_url }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                                <i class="ri-eye-line me-1"></i> Preview
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Not Uploaded</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(!$item)
                                                            <span class="badge bg-secondary">Not Uploaded</span>
                                                        @elseif($item->status === 'approved')
                                                            <span class="badge bg-success">Approved</span>
                                                        @elseif($item->status === 'rejected')
                                                            <span class="badge bg-danger">Rejected</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $item->remarks ?? '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
        function setDocStatus(id, status) {
            var select = document.getElementById('selectStatus' + id);
            if (select) {
                select.value = status;
            }
            var hidden = document.getElementById('modalStatus' + id);
            if (hidden) {
                hidden.value = status;
            }
        }
    </script>
</x-master-layout>
