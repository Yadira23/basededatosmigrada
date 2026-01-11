<!-- Modal -->
  @if (session()->has('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<div wire:ignore.self class="modal fade" id="DataModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="DataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DataModalLabel">{{ $selected_id ? 'Update Dependencia' : 'Create Dependencia' }}</h5>
                <button wire:click.prevent="cancel()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
           <div class="modal-body">
				<form>
                @if ($selected_id)
                    <input type="hidden" wire:model="selected_id">
                @endif
                    <div class="form-group">
                        <label for="nombre_depen"></label>
                        <input wire:model.live="nombre_depen" type="text" class="form-control" oninput="this.value=this.value.replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]/g,'')" placeholder="Nombre Depen">@error('nombre_depen') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
    <label for="id_sector">Sector</label>

    <select wire:model.live="id_sector" class="form-control" id="id_sector">
        <option value="">Seleccione un sector</option>

        @foreach($sector as $sector)
            <option value="{{ $sector->id_sector }}">
                {{ $sector->nombre_sector }}
            </option>
        @endforeach
    </select>

    @error('id_sector')
        <span class="error text-danger">{{ $message }}</span>
    @enderror
</div>
                    <div class="form-group">
                        <label for="email_depen"></label>
                        <input wire:model.live="email_depen" type="text" class="form-control" id="email_depen" placeholder="Email Depen">@error('email_depen') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="extension_depen"></label>
                        <input wire:model.live="extension_depen" type="text" maxlength="6" inputmode="numeric" class="form-control" oninput="this.value=this.value.replace(/[^0-9]/g,'')" placeholder="Extension Depen">@error('extension_depen') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="telefono_depen"></label>
                        <input wire:model.live="telefono_depen" type="text" class="form-control" maxlength="10" inputmode="numeric" placeholder="Telefono Depen" pattern="[0-9]*" oninput="this.value=this.value.replace(/[^0-9]/g,'')">@error('telefono_depen') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="calle_depen"></label>
                        <input wire:model.live="calle_depen" type="text" class="form-control" id="calle_depen" placeholder="Calle Depen">@error('calle_depen') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="numerocalle_depen"></label>
                        <input wire:model.live="numerocalle_depen" type="text" maxlength="5" inputmode="numeric" class="form-control" oninput="this.value=this.value.replace(/[^0-9]/g,'')" placeholder="Numero calle Depen">@error('numerocalle_depen') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="colonia_depen"></label>
                        <input wire:model.live="colonia_depen" type="text" class="form-control" oninput="this.value=this.value.replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]/g,'')" placeholder="Colonia Depen">@error('colonia_depen') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="cp_depen"></label>
                        <input wire:model.live="cp_depen" type="text" maxlength="5" inputmode="numeric" class="form-control" oninput="this.value=this.value.replace(/[^0-9]/g,'')" placeholder="Cp Depen">@error('cp_depen') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Close</button>
                <button type="button" wire:click.prevent="save()" class="btn btn-primary">{{ $selected_id ? 'Update' : 'Create' }}</button>
            </div>
        </div>
    </div>
</div>