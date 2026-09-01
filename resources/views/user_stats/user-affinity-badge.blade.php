<div>
    @if(!is_null($affinityPercentage))
    <div class="card bg-dark text-white border-secondary shadow-sm p-3 mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h6 class="mb-0 text-muted small uppercase">Compatibilità Nakama</h6>
                <span class="fs-5 fw-bold">Affinità con te</span>
            </div>

            <div class="text-end">
                <span class="badge 
                        {{ $affinityPercentage >= 75 ? 'bg-success' : ($affinityPercentage >= 45 ? 'bg-warning text-dark' : 'bg-secondary') }} 
                        fs-5 px-3 py-2 rounded-pill shadow-sm">
                    🔥 {{ $affinityPercentage }}%
                </span>
            </div>
        </div>

        <!-- Mini Progress Bar -->
        <div class="progress mt-2" style="height: 6px;">
            <div class="progress-bar {{ $affinityPercentage >= 75 ? 'bg-success' : ($affinityPercentage >= 45 ? 'bg-warning' : 'bg-secondary') }}"
                role="progressbar"
                style="width: {{ $affinityPercentage }}%"
                aria-valuenow="{{ $affinityPercentage }}"
                aria-valuemin="0"
                aria-valuemax="100">
            </div>
        </div>
    </div>
    @endif
</div>