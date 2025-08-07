<?php

namespace App\Http\Controllers;

use App\Http\Requests\Region\StoreRequest;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $regions = Region::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        })
            ->paginate(10);

        return view('pages.region.index', [
            'regions' => $regions,
            'search' => $search
        ]);
    }

    public function create() {

        $regionAdmins = User::where('role', 'region_admin')->get();

        return view('pages.region.create', [
            'regionAdmins' => $regionAdmins
        ]);
    }

    public  function store(StoreRequest $request) {

        $data = $request->validated();

        $data['is_active'] = $request->has('is_active') ? 1 : 0;


        $region = Region::firstOrCreate($data);

        $region->save();

        return redirect()->route('regions.index');
    }

    public function edit(Region $region) {
        return view('pages.region.edit', [
            'region' => $region
        ]);
    }

    public function update(StoreRequest $request, Region $region)
    {
        $data = $request->validated();

        // Обработка чекбокса is_active
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $region->update($data);

        return redirect()->route('regions.index');
    }

    public function destroy(Region $region)
    {
        try {
            // Сохраняем ID пользователя, который удаляет регион
            $region->deleted_by = auth()->id();
            $region->save();

            // Выполняем мягкое удаление
            $region->delete();

            return redirect()->route('regions.index')
                ->with('success', 'Регион успешно удален');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Ошибка при удалении региона: ' . $e->getMessage());
        }
    }

    public function export(): StreamedResponse
    {
        $regions = Region::withCount(['users', 'translators', 'proofreaders'])->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="regions_export_' . date('Y-m-d') . '.csv"',
        ];

        return new StreamedResponse(function() use ($regions) {
            $handle = fopen('php://output', 'w');

            // Заголовки CSV (соответствуют колонкам таблицы)
            fputcsv($handle, [
                'ID',
                'Регион',
                'Пользователи',
                'Переводчики',
                'Корректоры',
                'Переведено',
                'На проверке',
                'Статус',
                'Дата создания'
            ], ';');

            // Данные
            foreach ($regions as $region) {
                $stats = $region->getTranslationStats();

                fputcsv($handle, [
                    $region->id,
                    $region->name,
                    $region->users_count,
                    $region->translators_count,
                    $region->proofreaders_count,
                    $stats['translated'] ?? 0,
                    $stats['proofread'] ?? 0,
                    $region->is_active ? 'Активен' : 'Неактивен',
                    $region->created_at->format('d.m.Y')
                ], ';');
            }

            fclose($handle);
        }, 200, $headers);
    }
}
