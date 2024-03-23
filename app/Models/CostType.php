<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'desc',
        'user_id'
    ];
    public static function addDefaultCostTypes(User $user): void
    {
        $costTypes = [
            ['name' => 'Mieszkanie / Nieruchomość', 'description' => 'Opłaty za czynsz, kredyt hipoteczny, ubezpieczenie mieszkania.'],
            ['name' => 'Rachunki za Usługi Publiczne', 'description' => 'Prąd, gaz, woda, śmieci, internet, telefon.'],
            ['name' => 'Jedzenie', 'description' => 'Zakupy spożywcze, restauracje, posiłki na wynos.'],
            ['name' => 'Odzież i Obuwie', 'description' => 'Zakupy odzieży, butów, akcesoriów modowych.'],
            ['name' => 'Transport', 'description' => 'Paliwo, opłaty za autostrady, parking, bilety komunikacji publicznej.'],
            ['name' => 'Zdrowie', 'description' => 'Opłaty za wizyty lekarskie, leki, ubezpieczenie zdrowotne.'],
            ['name' => 'Rozrywka', 'description' => 'Kino, koncerty, wyjścia do restauracji, streaming platformy.'],
            ['name' => 'Edukacja', 'description' => 'Opłaty za szkołę, kursy, materiały edukacyjne.'],
            ['name' => 'Technologia', 'description' => 'Zakupy sprzętu elektronicznego, oprogramowania, akcesoria.'],
            ['name' => 'Uroda i Higiena', 'description' => 'Kosmetyki, produkty do pielęgnacji, wizyty u fryzjera, kosmetyczki.'],
            ['name' => 'Sport i Rekreacja', 'description' => 'Kluby fitness, opłaty za basen, wyposażenie sportowe.'],
            ['name' => 'Domowe Naprawy i Utrzymanie', 'description' => 'Narzędzia, materiały budowlane, usługi remontowe.'],
            ['name' => 'Opieka nad Zwierzętami', 'description' => 'Jedzenie dla zwierząt, weterynarz, akcesoria dla zwierząt.'],
            ['name' => 'Prezenty i Święta', 'description' => 'Zakupy prezentów, dekoracje, wydatki związane ze świętami.'],
            ['name' => 'Ubezpieczenia', 'description' => 'Ubezpieczenie zdrowotne, ubezpieczenie samochodu, ubezpieczenie życiowe.'],
            ['name' => 'Oszczędności i Inwestycje', 'description' => 'Regularne oszczędzanie, inwestowanie w fundusze, akcje, obligacje.'],
            ['name' => 'Opłaty Bankowe i Finansowe', 'description' => 'Opłaty za konto bankowe, prowizje za przelewy, kredyty, odsetki.'],
            ['name' => 'Hobby i Zainteresowania', 'description' => 'Wydatki związane z hobby, kursy, sprzęt, akcesoria.'],
            ['name' => 'Podatki', 'description' => 'Podatek dochodowy, podatek od nieruchomości.'],
            ['name' => 'Media', 'description' => 'Czasopisma, gazety, książki, filmy, muzyka.'],
            ['name' => 'Darowizny i Działalność Charytatywna', 'description' => 'Wsparcie organizacji charytatywnych, donacje.'],
            ['name' => 'Pieniądze Wolne', 'description' => 'Gotówka na niespodziewane wydatki, awaryjne fundusze.'],
        ];

        foreach ($costTypes as $costType) {
            $user->costTypes()->create([
                'name' => $costType['name'],
                'desc' => $costType['description'],
                'user_id' => $user->id
            ]);
        }
    }
    protected static function boot()
    {
        parent::boot();
        self::creating(function ($model){
            $model->user_id = auth()->id();
        });

        self::addGlobalScope(function(Builder $builder){
            $builder->where('user_id', auth()->id());
        });
    }
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
