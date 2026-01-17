<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Univerzitet;
use Illuminate\Validation\Rule;



class UniverzitetController extends Controller
{
  public function create()
    {
        $countries = [
'Afganistan', 'Albanija', 'Alžir', 'Andora', 'Angola', 'Antigva i Barbuda',
'Argentina', 'Armenija', 'Australija', 'Austrija', 'Azerbejdžan',
'Bahami', 'Bahrein', 'Bangladeš', 'Barbados', 'Belgija', 'Belize',
'Benin', 'Bolivija', 'Bosna i Hercegovina', 'Bocvana',
'Brazil', 'Brunej', 'Bugarska', 'Burkina Faso', 'Burundi', 'Belorusija', 'Butan',
'Crna Gora', 'Čad', 'Češka', 'Čile', 'Kipar',
'Danska', 'Demokratska Republika Kongo', 'Džibuti', 'Dominika', 'Dominikanska Republika',
'Egipat', 'Ekvador', 'Ekvatorijalna Gvineja', 'Eritreja', 'Estonija', 'Eswatini', 'Etiopija',
'Fidži', 'Filipini', 'Finska', 'Francuska',
'Gabon', 'Gambija', 'Gana', 'Grčka', 'Grenada', 'Gruzija', 'Gvajana', 'Gvatemala',
'Gvineja', 'Gvineja-Bisao',
'Haiti', 'Honduras', 'Holandija', 'Hrvatska',
'Indija', 'Indonezija', 'Irak', 'Iran', 'Irska', 'Island', 'Italija', 'Izrael',
'Jamajka', 'Japan', 'Jemen', 'Jordan',
'Južna Afrika', 'Južni Sudan',
'Kambodža', 'Kamerun', 'Kanada', 'Katar', 'Kazahstan', 'Kenija', 'Kina',
'Kirgistan', 'Kiribati', 'Kolumbija', 'Komori', 'Kongo', 'Kostarika', 'Kuba', 'Kuvajt',
'Laos', 'Letonija', 'Lesoto', 'Liban', 'Liberija', 'Libija',
'Lihtenštajn', 'Litvanija', 'Luksemburg',
'Madagaskar', 'Mađarska', 'Malavi', 'Maldivi', 'Malezija', 'Mali', 'Malta',
'Maroko', 'Maršalska Ostrva', 'Mauricijus', 'Mauritanija', 'Meksiko',
'Mikronezija', 'Mjanmar', 'Moldavija', 'Monako', 'Mongolija', 'Mozambik',
'Namibija', 'Nauru', 'Nepal', 'Njemačka', 'Niger', 'Nigerija',
'Nikaragva', 'Norveška', 'Novi Zeland',
'Obala Slonovače', 'Oman',
'Pakistan', 'Palau', 'Panama', 'Papua Nova Gvineja', 'Paragvaj', 'Peru',
'Poljska', 'Portugal',
'Ruanda', 'Rumunija', 'Rusija',
'Salvador', 'Samoa', 'San Marino', 'Saudijska Arabija', 'Sejšeli', 'Senegal',
'Sijera Leone', 'Singapur', 'Sirija', 'Sjedinjene Američke Države',
'Severna Koreja', 'Severna Makedonija', 'Slovačka', 'Slovenija',
'Somalija', 'Srbija', 'Sudan', 'Surinam',
'Španija', 'Šri Lanka', 'Svedska', 'Švajcarska',
'Tadžikistan', 'Tajland', 'Tanzanija', 'Togo', 'Tonga',
'Trinidad i Tobago', 'Tunis', 'Turska', 'Turkmenistan', 'Tuvalu',
'Uganda', 'Ukrajina', 'Ujedinjeni Arapski Emirati', 'Ujedinjeno Kraljevstvo',
'Urugvaj', 'Uzbekistan',
'Vanuatu', 'Vatikan', 'Venecuela', 'Vijetnam',
'Zambija', 'Zelenortska Ostrva', 'Zimbabve',
'Palestina',
        ];
        sort($countries);
        return view('univerzitet.create', compact('countries'));
    }

    public function index()
    {
        $univerziteti = Univerzitet::all();
        $countries = [
'Afganistan', 'Albanija', 'Alžir', 'Andora', 'Angola', 'Antigva i Barbuda',
'Argentina', 'Armenija', 'Australija', 'Austrija', 'Azerbejdžan',
'Bahami', 'Bahrein', 'Bangladeš', 'Barbados', 'Belgija', 'Belize',
'Benin', 'Bolivija', 'Bosna i Hercegovina', 'Bocvana',
'Brazil', 'Brunej', 'Bugarska', 'Burkina Faso', 'Burundi', 'Belorusija', 'Butan',
'Crna Gora', 'Čad', 'Češka', 'Čile', 'Kipar',
'Danska', 'Demokratska Republika Kongo', 'Džibuti', 'Dominika', 'Dominikanska Republika',
'Egipat', 'Ekvador', 'Ekvatorijalna Gvineja', 'Eritreja', 'Estonija', 'Eswatini', 'Etiopija',
'Fidži', 'Filipini', 'Finska', 'Francuska',
'Gabon', 'Gambija', 'Gana', 'Grčka', 'Grenada', 'Gruzija', 'Gvajana', 'Gvatemala',
'Gvineja', 'Gvineja-Bisao',
'Haiti', 'Honduras', 'Holandija', 'Hrvatska',
'Indija', 'Indonezija', 'Irak', 'Iran', 'Irska', 'Island', 'Italija', 'Izrael',
'Jamajka', 'Japan', 'Jemen', 'Jordan',
'Južna Afrika', 'Južni Sudan',
'Kambodža', 'Kamerun', 'Kanada', 'Katar', 'Kazahstan', 'Kenija', 'Kina',
'Kirgistan', 'Kiribati', 'Kolumbija', 'Komori', 'Kongo', 'Kostarika', 'Kuba', 'Kuvajt',
'Laos', 'Letonija', 'Lesoto', 'Liban', 'Liberija', 'Libija',
'Lihtenštajn', 'Litvanija', 'Luksemburg',
'Madagaskar', 'Mađarska', 'Malavi', 'Maldivi', 'Malezija', 'Mali', 'Malta',
'Maroko', 'Maršalska Ostrva', 'Mauricijus', 'Mauritanija', 'Meksiko',
'Mikronezija', 'Mjanmar', 'Moldavija', 'Monako', 'Mongolija', 'Mozambik',
'Namibija', 'Nauru', 'Nepal', 'Njemačka', 'Niger', 'Nigerija',
'Nikaragva', 'Norveška', 'Novi Zeland',
'Obala Slonovače', 'Oman',
'Pakistan', 'Palau', 'Panama', 'Papua Nova Gvineja', 'Paragvaj', 'Peru',
'Poljska', 'Portugal',
'Ruanda', 'Rumunija', 'Rusija',
'Salvador', 'Samoa', 'San Marino', 'Saudijska Arabija', 'Sejšeli', 'Senegal',
'Sijera Leone', 'Singapur', 'Sirija', 'Sjedinjene Američke Države',
'Severna Koreja', 'Severna Makedonija', 'Slovačka', 'Slovenija',
'Somalija', 'Srbija', 'Sudan', 'Surinam',
'Španija', 'Šri Lanka', 'Svedska', 'Švajcarska',
'Tadžikistan', 'Tajland', 'Tanzanija', 'Togo', 'Tonga',
'Trinidad i Tobago', 'Tunis', 'Turska', 'Turkmenistan', 'Tuvalu',
'Uganda', 'Ukrajina', 'Ujedinjeni Arapski Emirati', 'Ujedinjeno Kraljevstvo',
'Urugvaj', 'Uzbekistan',
'Vanuatu', 'Vatikan', 'Venecuela', 'Vijetnam',
'Zambija', 'Zelenortska Ostrva', 'Zimbabve',
'Palestina',
        ];
        sort($countries);
        return view('univerzitet.index', compact('univerziteti', 'countries'));
    }

    public function store(Request $request)
    {
          $validated = $request->validate([
        'naziv' => 'required|string|max:255',
        'drzava' => 'required|string|max:255',
        'grad' => 'required|string|max:255',
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('univerziteti', 'email')
        ],
    ], [
        'email.unique' => 'Univerzitet sa ovim emailom već postoji u bazi.',
    ]);

        $univerzitet = Univerzitet::create($validated);


            return redirect()->back()->with('success', 'Univerzitet uspješno dodat!');

    }

  

    public function show($id)
    {
        return Univerzitet::with('fakulteti')->findOrFail($id);
    }

public function edit($id)
{
    $univerzitet = Univerzitet::findOrFail($id);
    $countries = [
'Afganistan', 'Albanija', 'Alžir', 'Andora', 'Angola', 'Antigva i Barbuda',
'Argentina', 'Armenija', 'Australija', 'Austrija', 'Azerbejdžan',
'Bahami', 'Bahrein', 'Bangladeš', 'Barbados', 'Belgija', 'Belize',
'Benin', 'Bolivija', 'Bosna i Hercegovina', 'Bocvana',
'Brazil', 'Brunej', 'Bugarska', 'Burkina Faso', 'Burundi', 'Belorusija', 'Butan',
'Crna Gora', 'Čad', 'Češka', 'Čile', 'Kipar',
'Danska', 'Demokratska Republika Kongo', 'Džibuti', 'Dominika', 'Dominikanska Republika',
'Egipat', 'Ekvador', 'Ekvatorijalna Gvineja', 'Eritreja', 'Estonija', 'Eswatini', 'Etiopija',
'Fidži', 'Filipini', 'Finska', 'Francuska',
'Gabon', 'Gambija', 'Gana', 'Grčka', 'Grenada', 'Gruzija', 'Gvajana', 'Gvatemala',
'Gvineja', 'Gvineja-Bisao',
'Haiti', 'Honduras', 'Holandija', 'Hrvatska',
'Indija', 'Indonezija', 'Irak', 'Iran', 'Irska', 'Island', 'Italija', 'Izrael',
'Jamajka', 'Japan', 'Jemen', 'Jordan',
'Južna Afrika', 'Južni Sudan',
'Kambodža', 'Kamerun', 'Kanada', 'Katar', 'Kazahstan', 'Kenija', 'Kina',
'Kirgistan', 'Kiribati', 'Kolumbija', 'Komori', 'Kongo', 'Kostarika', 'Kuba', 'Kuvajt',
'Laos', 'Letonija', 'Lesoto', 'Liban', 'Liberija', 'Libija',
'Lihtenštajn', 'Litvanija', 'Luksemburg',
'Madagaskar', 'Mađarska', 'Malavi', 'Maldivi', 'Malezija', 'Mali', 'Malta',
'Maroko', 'Maršalska Ostrva', 'Mauricijus', 'Mauritanija', 'Meksiko',
'Mikronezija', 'Mjanmar', 'Moldavija', 'Monako', 'Mongolija', 'Mozambik',
'Namibija', 'Nauru', 'Nepal', 'Njemačka', 'Niger', 'Nigerija',
'Nikaragva', 'Norveška', 'Novi Zeland',
'Obala Slonovače', 'Oman',
'Pakistan', 'Palau', 'Panama', 'Papua Nova Gvineja', 'Paragvaj', 'Peru',
'Poljska', 'Portugal',
'Ruanda', 'Rumunija', 'Rusija',
'Salvador', 'Samoa', 'San Marino', 'Saudijska Arabija', 'Sejšeli', 'Senegal',
'Sijera Leone', 'Singapur', 'Sirija', 'Sjedinjene Američke Države',
'Severna Koreja', 'Severna Makedonija', 'Slovačka', 'Slovenija',
'Somalija', 'Srbija', 'Sudan', 'Surinam',
'Španija', 'Šri Lanka', 'Svedska', 'Švajcarska',
'Tadžikistan', 'Tajland', 'Tanzanija', 'Togo', 'Tonga',
'Trinidad i Tobago', 'Tunis', 'Turska', 'Turkmenistan', 'Tuvalu',
'Uganda', 'Ukrajina', 'Ujedinjeni Arapski Emirati', 'Ujedinjeno Kraljevstvo',
'Urugvaj', 'Uzbekistan',
'Vanuatu', 'Vatikan', 'Venecuela', 'Vijetnam',
'Zambija', 'Zelenortska Ostrva', 'Zimbabve',
'Palestina',
    ];
    sort($countries);
    return view('univerzitet.edit', compact('univerzitet', 'countries'));
}

public function update(Request $request, $id)
{
    $univerzitet = Univerzitet::findOrFail($id);

    $validated = $request->validate([
        'naziv' => 'required|string|max:255',
        'drzava' => 'required|string|max:255',
        'grad' => 'required|string|max:255',
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('univerziteti')->ignore($univerzitet->id),
        ],
    ], [
        'email.unique' => 'Univerzitet sa ovim emailom već postoji u bazi.',
    ]);

    $univerzitet->update($validated);

    return redirect()->route('univerzitet.index')->with('success', 'Univerzitet uspješno ažuriran!');
}


public function destroy($id)
{
    $univerzitet = Univerzitet::findOrFail($id);

    try {
        $univerzitet->delete();
        return redirect()->route('univerzitet.index')->with('success', 'Univerzitet je uspješno obrisan!');
    } catch (\Illuminate\Database\QueryException $e) {
        // Provjera da li je zbog foreign key constraint-a
        if($e->getCode() == '23000') {
            return redirect()->route('univerzitet.index')->with('error', 'Ne možete obrisati univerzitet jer postoje fakulteti koji pripadaju njemu.');
        }
        throw $e; // Ostale greške ponovo baci
    }
}


}
