<?php

namespace App\Http\Controllers;

use App\Eloquents\License;
use App\Utilities\Prefecture;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LicenseController extends Controller
{
    /* @var License */
    private $license;
    /** @var int */
    private $pagination;

    /**
     * @param License $license
     */
    public function __construct(License $license)
    {
        $this->license = $license;
        $this->pagination = env('API_PAGINATION');
    }

    /**
     * @return LengthAwarePaginator
     */
    public function list(): LengthAwarePaginator
    {
        return $this->license->latest()->paginate($this->pagination);
    }

    /**
     * LumenではEloquentのメソッドインジェクション非対応
     * @param string $licenseId
     * @return License
     */
    public function show(string $licenseId): License
    {
        return $this->license->with($this->with())->findOrFail($licenseId);
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     * @throws ValidationException
     */
    public function search(Request $request): LengthAwarePaginator
    {
        $this->validate($request, [
            'drink_type_id' => ['nullable', Rule::exists('drink_types', 'id')],
            'prefecture' => ['nullable', Rule::in(Prefecture::getKeys())],
            'name' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string', 'max:255'],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date'],
        ]);

        return $this->buildQuery($request)->paginate($this->pagination);
    }

    /**
     * @param Request $request
     * @return Builder
     */
    private function buildQuery(Request $request): Builder
    {
        $query = $this->license->query();

        if ($request->drink_type_id) {
            $query->where('drink_type_id', $request->drink_type_id);

        }
        if ($request->prefecture) {
            $query->where('prefecture', $request->prefecture);
        }
        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->address) {
            $query->where('address', 'like', '%' . $request->address . '%');
        }
        if ($request->start || $request->end) {
            $between = [
                'start' => $request->start ?? Carbon::parse('1900-01-01'),
                'end' => $request->end ?? Carbon::now(),
            ];
            $query->whereBetween('permitted_at', $between);
        }

        return $query;
    }

    /**
     * @return array
     */
    private function with(): array
    {
        return [
            'drinkType',
        ];
    }
}
