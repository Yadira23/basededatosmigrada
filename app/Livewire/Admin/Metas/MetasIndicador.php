<?php

namespace App\Livewire\Admin\Metas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Indicador;
use App\Models\Meta;
use App\Models\Formulario;

class MetasIndicador extends Component
{
    use WithPagination;

    public int $id_ind;

    public $indicador;

    // para tabla/listado
    public string $keyWord = '';

    // modal create/edit
    public ?int $selectedId = null; // metas.id
    public string $titulo = '';
    public ?int $ejercicio = null;   // 2026
    public ?int $corte = null;       // 1..12 | 1..4 | 1..2 | 1

    // para llenar el select de corte según el periodo del indicador
    public array $cortesDisponibles = [];
    public array $cortesUsados = [];

    // modal campos
    public bool $camposModalOpen = false;
    public ?int $metaCamposId = null;
    public array $campos = []; // array de campos [{slug,label,type,required,min,max}]

    protected $paginationTheme = 'bootstrap';

    public function mount($id_ind)
    {
        $this->id_ind = (int) $id_ind;

        $this->indicador = Indicador::findOrFail($this->id_ind);

        // Defaults
        $this->ejercicio = (int) date('Y');
        $this->buildCortesDisponibles();
        $this->loadCortesUsados();

        if ($this->corte === null) {
            // primer corte por defecto
            $firstKey = array_key_first($this->cortesDisponibles);
            $this->corte = $firstKey !== null ? (int) $firstKey : 1;
        }
    }

    public function updatedKeyWord()
    {
        $this->resetPage();
    }

    private function buildCortesDisponibles(): void
    {
        $p = strtolower((string) $this->indicador->periodo_ind);

        // Ajusta aquí el nombre real del campo del periodo del indicador:
        // puede ser periodo_ind, periodo, periodo_form, etc.

        $map = [];

        if (str_contains($p, 'mens')) {
            // Mensual: 1..12
            for ($i = 1; $i <= 12; $i++) $map[$i] = 'M' . str_pad((string)$i, 2, '0', STR_PAD_LEFT);
        } elseif (str_contains($p, 'trim')) {
            // Trimestral: 1..4
            for ($i = 1; $i <= 4; $i++) $map[$i] = 'T' . $i;
        } elseif (str_contains($p, 'sem')) {
            // Semestral: 1..2
            for ($i = 1; $i <= 2; $i++) $map[$i] = 'S' . $i;
        } elseif (str_contains($p, 'an')) {
            // Anual: solo 1
            $map[1] = 'Anual';
        } else {
            // fallback: anual
            $map[1] = 'Anual';
        }

        $this->cortesDisponibles = $map;

        // Si el corte actual ya no existe, lo ajustamos
        if ($this->corte !== null && !array_key_exists($this->corte, $this->cortesDisponibles)) {
            $this->corte = (int) array_key_first($this->cortesDisponibles);
        }
    }

    private function loadCortesUsados(): void
    {
        $this->cortesUsados = Meta::where('id_ind', $this->id_ind)
            ->where('ejercicio', $this->ejercicio)
            ->pluck('corte')
            ->map(fn($v) => (int) $v)
            ->toArray();
    }

    private function resetInput()
    {
        $this->selectedId = null;
        $this->titulo = '';
        $this->ejercicio = (int) date('Y');

        $this->buildCortesDisponibles();
        $firstKey = array_key_first($this->cortesDisponibles);
        $this->corte = $firstKey !== null ? (int) $firstKey : 1;
    }

    public function updatedEjercicio()
    {
        $this->loadCortesUsados();

        // si el corte actual está ocupado, selecciona el primero libre
        if (in_array((int)$this->corte, $this->cortesUsados, true)) {
            foreach ($this->cortesDisponibles as $key => $label) {
                if (!in_array((int)$key, $this->cortesUsados, true)) {
                    $this->corte = (int) $key;
                    break;
                }
            }
        }
    }

    // ===== CRUD Meta =====

    public function create()
    {
        $this->resetInput();
    }

    public function store()
    {
        $this->buildCortesDisponibles();

        $this->validate([
            'titulo' => 'required|string|max:200',
            'ejercicio' => 'required|integer|min:2000|max:2100',
            'corte' => 'required|integer',
        ]);

        // validar que el corte exista en el mapa actual
        if (!array_key_exists((int)$this->corte, $this->cortesDisponibles)) {
            $this->addError('corte', 'El corte no es válido para el periodo del indicador.');
            return;
        }

        // 🚫 NO permitir repetir corte por indicador + ejercicio
        $yaExiste = Meta::where('id_ind', $this->id_ind)
            ->where('ejercicio', $this->ejercicio)
            ->where('corte', $this->corte)
            ->exists();

        if ($yaExiste) {
            $this->addError('corte', 'Ese periodo ya tiene una meta registrada para este ejercicio.');
            return;
        }

        // ✅ orden automático por indicador
        $nextOrden = (int) (Meta::where('id_ind', $this->id_ind)->max('orden') ?? 0) + 1;

        Meta::create([
            'id_ind' => $this->id_ind,
            'titulo' => $this->titulo,
            'ejercicio' => $this->ejercicio,
            'corte' => $this->corte,
            'orden' => $nextOrden,
            'config_campos' => [], // arranca vacío
        ]);

        session()->flash('message', 'Meta creada correctamente.');
        $this->dispatch('close-modal');
        $this->resetInput();
    }

    public function edit($id)
    {
        $m = Meta::where('id_ind', $this->id_ind)->findOrFail($id);

        $this->selectedId = (int) $m->id;
        $this->titulo = (string) $m->titulo;
        $this->ejercicio = (int) ($m->ejercicio ?? date('Y'));
        $this->corte = (int) ($m->corte ?? 1);

        $this->buildCortesDisponibles();
    }

    public function update()
    {
        $this->buildCortesDisponibles();

        $this->validate([
            'titulo' => 'required|string|max:200',
            'ejercicio' => 'required|integer|min:2000|max:2100',
            'corte' => 'required|integer',
        ]);

        if (!array_key_exists((int)$this->corte, $this->cortesDisponibles)) {
            $this->addError('corte', 'El corte no es válido para el periodo del indicador.');
            return;
        }

        $yaExiste = Meta::where('id_ind', $this->id_ind)
            ->where('ejercicio', $this->ejercicio)
            ->where('corte', $this->corte)
            ->where('id', '!=', $this->selectedId)
            ->exists();

        if ($yaExiste) {
            $this->addError('corte', 'Ese periodo ya tiene una meta registrada para este ejercicio.');
            return;
        }

        $m = Meta::where('id_ind', $this->id_ind)->findOrFail($this->selectedId);

        $m->update([
            'titulo' => $this->titulo,
            'ejercicio' => $this->ejercicio,
            'corte' => $this->corte,
        ]);

        session()->flash('message', 'Meta actualizada.');
        $this->dispatch('close-modal');
        $this->resetInput();
    }

    public function destroy($id)
    {
        $m = Meta::where('id_ind', $this->id_ind)->findOrFail($id);
        $m->delete();

        session()->flash('message', 'Meta eliminada.');
    }

    // ===== Campos (config_campos) =====

    public function openCampos($metaId)
    {
        $m = Meta::where('id_ind', $this->id_ind)->findOrFail($metaId);

        $this->metaCamposId = (int) $m->id;

        // config_campos puede venir string o array: lo normalizamos a array
        $raw = $m->config_campos;
        if (is_string($raw)) {
            $raw = json_decode($raw, true) ?: [];
        }
        if (!is_array($raw)) $raw = [];

        $this->campos = $raw;

        // si está vacío, arranca con 1 fila
        if (empty($this->campos)) {
            $this->campos = [[
                'slug' => '',
                'label' => '',
                'type' => 'number',
                'required' => true,
                'min' => 0,
                'max' => null,
            ]];
        }
    }

    public function addCampo()
    {
        $this->campos[] = [
            'slug' => '',
            'label' => '',
            'type' => 'number',
            'required' => false,
            'min' => null,
            'max' => null,
        ];
    }

    public function removeCampo($index)
    {
        unset($this->campos[$index]);
        $this->campos = array_values($this->campos);
    }

    public function saveCampos()
    {
        if (!$this->metaCamposId) return;

        // validación simple: slug requerido y único
        $slugs = [];
        foreach ($this->campos as $i => $c) {
            $slug = trim((string)($c['slug'] ?? ''));
            if ($slug === '') {
                throw new \Exception("El campo #" . ($i + 1) . " necesita slug.");
            }
            if (in_array($slug, $slugs, true)) {
                throw new \Exception("Slug repetido: {$slug}.");
            }
            $slugs[] = $slug;
        }

        $m = Meta::where('id_ind', $this->id_ind)->findOrFail($this->metaCamposId);

        $m->update([
            'config_campos' => $this->campos,
        ]);

        session()->flash('message', 'Campos de la meta guardados.');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        $q = trim($this->keyWord);

        $metas = Meta::with('indicador')
            ->where('id_ind', $this->id_ind)
            ->when($q !== '', function ($query) use ($q) {
                $query->where('titulo', 'like', "%{$q}%")
                    ->orWhere('ejercicio', 'like', "%{$q}%")
                    ->orWhere('corte', 'like', "%{$q}%");
            })
            ->orderBy('orden')
            ->paginate(10);

        $cortesUsados = Meta::where('id_ind', $this->id_ind)
            ->where('ejercicio', $this->ejercicio)
            ->pluck('corte')
            ->toArray();

        return view('livewire.metas.view', [
            'metas' => $metas,
            'indicador' => $this->indicador,
            'cortesUsados' => $cortesUsados,
        ])->extends('layouts.app')->section('content');
    }
}
