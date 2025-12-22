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
            'Afganistan', 'Albanija', 'Alžir', 'Andora', 'Angola', 'Antigua i Barbuda',
            'Argentina', 'Armenija', 'Australija', 'Austrija', 'Azerbejdžan',
            'Bahami', 'Bahrein', 'Bangladeš', 'Barbados', 'Belgija', 'Belize',
            'Benin', 'Butan', 'Bjelorusija', 'Bolivija', 'Bosna i Hercegovina', 'Bocvana',
            'Brazil', 'Brunej', 'Bugarska',
            'Burkina Faso', 'Burundi', 'Crna Gora' ,
            'Čad', 'Češka', 'Čile', 'Cipar',
            'Demokratska Republika Kongo', 'Danska', 'Džibuti', 'Dominika', 'Dominikanska Republika',
            'Egipat', 'Ekvador', 'Ekvatrijalna Gvineja', 'Eritreja', 'Estonija', 'Etiopija',
            'Fidži', 'Filipini', 'Finska', 'Francuska',
            'Gabon', 'Gambija', 'Gana', 'Gvajana', 'Gvineja', 'Gvineja-Bisao',
            'Haiti', 'Honduras', 'Hrvatska',
            'Indija', 'Indonezija', 'Irak', 'Iran', 'Irska', 'Islanda', 'Italija', 'Izrael',
            'Jamajka', 'Japan', 'Jemen', 'Jordanija',
            'Kazahstan', 'Kambodža', 'Kamerun', 'Kanada', 'Kapverdski otoci', 'Katar', 'Kenija', 'Kirgistan',
            'Kiribati', 'Kina', 'Kipar', 'Sjeverna Koreja', 'Juzna Koreja', 'Kostarika', 'Kuba',
            'Kuvajt',
            'Laos', 'Lesoto', 'Latvija', 'Liban', 'Liberija', 'Lihtenštajn', 'Litvanija', 'Luksemburg',
            'Libija',
            'Macao', 'Madagaskar', 'Makedonija', 'Malavi', 'Malezija', 'Mali', 'Malta', 'Malte',
            'Marooko', 'Maršalovi otoci', 'Mauricijus', 'Mauritanija', 'Mađarska', 'Meksiko',
            'Međunarodno područje', 'Mikronezija', 'Mjanmar', 'Moldavija', 'Monako', 'Mongolija',
            'Montserrat', 'Mozambik',
            'Namibija', 'Nauru', 'Nepal', 'Njemačka', 'Nikaragva', 'Niger', 'Nigerija', 'Niue',
            'Novozelandija', 'Norveška',
            'Oland', 'Oman',
            'Palestina', 'Panama', 'Papua Nova Gvineja', 'Paragvaj', 'Peruanski', 'Peru', 'Poljska',
            'Portoriko', 'Portugalia',
            'Reagionalni položaj',
            'Réunion', 'Ruanda', 'Rumunija', 'Rusija',
            'Sahara', 'Salvadorski', 'Samoa', 'Samota Amerika', 'San Marino', 'Sankt Kitts i Nevis',
            'Sankt Lucia', 'Sankt Vincent i Grenadini', 'Sao Tome i Principe', 'Sau Tome',
            'Saudijska Arabija', 'Sveta Jelena', 'Sjedinjene države', 'Seves Jelenski ostaci', 'Seničel',
            'Senegal', 'Sent Barts', 'Sent Kristof i Nevis', 'Sent Luska', 'Sent Marten',
            'Sent Pjer i Mikelon', 'Sent Vinsent i Grenadini', 'Septembar', 'Serf',
            'Severna Karolina', 'Severna Marijanaska ostrva', 'Severnajkoreja', 'Severnajmakedonija',
            'Sijalje', 'Sijam', 'Sijera Leone', 'Singapur', 'Sirija', 'Sjedinjene države',
            'Slovenija', 'Slovačka', 'Somali', 'Srbija' , 'Srednja Afrička Republika', 'Surija',
            'Svalbard i Jan Majen', 'Sveti Bartolomej', 'Sveti Marko', 'Sveti Marten',
            'Sveća Jelenina',
            'Šotska', 'Španjolska', 'Švedska', 'Švicarska',
            'Tajland', 'Tajvan', 'Tanzanija', 'Teritori Francuske Polinezije', 'Timor Leste',
            'Togo', 'Tokelau', 'Tonga', 'Trinidad i Tobago', 'Tunis', 'Turska',
            'Turkmenistan', 'Turcistan', 'Tuvalu',
            'Uganada', 'Ugarska', 'Ukrajina', 'Ujedinjene Arapske Emirate', 'Ujedinjeno Kraljevstvo',
            'Urugvaj', 'Uzbekistan',
            'Vanuatu', 'Vatikan', 'Velika Britanija', 'Velika Britanija i Irska', 'Velika Britanija i Severna Irska',
            'Velika Britanija i Sjeverna Irska', 'Velika Britanija i Sjedinjene države',
            'Vels', 'Venecuela', 'Vijetnam',
            'Virginija', 'Zapad Samare',
            'Zajednica nezavisnih država', 'Zambija', 'Zapadna Sahara', 'Zapadna Samarija',
            'Zelenortski Otoci', 'Zimbabve', 'Žambasija',
        ];
        sort($countries);
        return view('univerzitet.create', compact('countries'));
    }

    public function index()
    {
        $univerziteti = Univerzitet::all();
        $countries = [
            'Afganistan', 'Albanija', 'Alžir', 'Andora', 'Angola', 'Antigua i Barbuda',
            'Argentina', 'Armenija', 'Australija', 'Austrija', 'Azerbejdžan',
            'Bahami', 'Bahrein', 'Bangladeš', 'Barbados', 'Belgija', 'Belize',
            'Benin', 'Butan', 'Bjelorusija', 'Bolivija', 'Bosna i Hercegovina', 'Bocvana',
            'Brazil', 'Brunej', 'Bugarska',
            'Burkina Faso', 'Burundi', 'Crna Gora' ,
            'Čad', 'Češka', 'Čile', 'Cipar',
            'Demokratska Republika Kongo', 'Danska', 'Džibuti', 'Dominika', 'Dominikanska Republika',
            'Egipat', 'Ekvador', 'Ekvatrijalna Gvineja', 'Eritreja', 'Estonija', 'Etiopija',
            'Fidži', 'Filipini', 'Finska', 'Francuska',
            'Gabon', 'Gambija', 'Gana', 'Gvajana', 'Gvineja', 'Gvineja-Bisao',
            'Haiti', 'Honduras', 'Hrvatska',
            'Indija', 'Indonezija', 'Irak', 'Iran', 'Irska', 'Islanda', 'Italija', 'Izrael',
            'Jamajka', 'Japan', 'Jemen', 'Jordanija',
            'Kazahstan', 'Kambodža', 'Kamerun', 'Kanada', 'Kapverdski otoci', 'Katar', 'Kenija', 'Kirgistan',
            'Kiribati', 'Kina', 'Kipar', 'Koreja', 'Koreja (Sjevernakoreja)', 'Kostarika', 'Kuba',
            'Kuvajt',
            'Laos', 'Lesoto', 'Latvija', 'Liban', 'Liberija', 'Lihtenštajn', 'Litvanija', 'Luksemburg',
            'Libija',
            'Macao', 'Madagaskar', 'Makedonija', 'Malavi', 'Malezija', 'Mali', 'Malta', 'Malte',
            'Marooko', 'Maršalovi otoci', 'Mauricijus', 'Mauritanija', 'Mađarska', 'Mehiko',
            'Međunarodno područje', 'Mikronezija', 'Mjanmar', 'Moldavija', 'Monako', 'Mongolija',
            'Montserrat', 'Mozambik',
            'Namibija', 'Nauru', 'Nepal', 'Nemačka', 'Nikaragva', 'Niger', 'Nigerija', 'Niue',
            'Novozelandija', 'Norveška',
            'Oland', 'Oman',
            'Palestina', 'Panama', 'Papua Nova Gvineja', 'Paragvaj', 'Peruanski', 'Peru', 'Poljska',
            'Portoriko', 'Portugalia',
            'Reagionalni položaj',
            'Réunion', 'Ruanda', 'Rumunija', 'Rusija',
            'Sahara', 'Salvadorski', 'Samoa', 'Samota Amerika', 'San Marino', 'Sankt Kitts i Nevis',
            'Sankt Lucia', 'Sankt Vincent i Grenadini', 'Sao Tome i Principe', 'Sau Tome',
            'Saudijska Arabija', 'Sveta Jelena', 'Sjedinjene države', 'Seves Jelenski ostaci', 'Seničel',
            'Senegal', 'Sent Barts', 'Sent Kristof i Nevis', 'Sent Luska', 'Sent Marten',
            'Sent Pjer i Mikelon', 'Sent Vinsent i Grenadini', 'Septembar', 'Serf',
            'Severna Karolina', 'Severna Marijanaska ostrva', 'Severnajkoreja', 'Severnajmakedonija',
            'Sijalje', 'Sijam', 'Sijera Leone','Singapur', 'Sirija', 'Sjedinjene države',
            'Slovenija', 'Slovačka', 'Somali',  'Srbija' ,'Srednja Afrička Republika', 'Surija',
            'Svalbard i Jan Majen', 'Sveti Bartolomej', 'Sveti Marko', 'Sveti Marten',
            'Sveća Jelenina',
            'Šotska', 'Španjolska', 'Švedska', 'Švicarska',
            'Tajland', 'Tajvan', 'Tanzanija', 'Teritori Francuske Polinezije', 'Timor Leste',
            'Togo', 'Tokelau', 'Tonga', 'Trinidad i Tobago', 'Tunis', 'Turska',
            'Turkmenistan', 'Turcistan', 'Tuvalu',
            'Uganada', 'Ugarska', 'Ukrajina', 'Ujedinjene Arapske Emirate', 'Ujedinjeno Kraljevstvo',
            'Urugvaj', 'Uzbekistan',
            'Vanuatu', 'Vatikan', 'Velika Britanija', 'Velika Britanija i Irska', 'Velika Britanija i Severna Irska',
            'Velika Britanija i Sjeverna Irska', 'Velika Britanija i Sjedinjene države',
            'Vels', 'Venecuela', 'Vijetnam',
            'Virginija', 'Zapad Samare',
            'Zajednica nezavisnih država', 'Zambija', 'Zapadna Sahara', 'Zapadna Samarija',
            'Zelenortski Otoci', 'Zimbabve', 'Žambasija',
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
        'Afganistan', 'Albanija', 'Alžir', 'Andora', 'Angola', 'Antigua i Barbuda',
        'Argentina', 'Armenija', 'Australija', 'Austrija', 'Azerbejdžan',
        'Bahami', 'Bahrein', 'Bangladeš', 'Barbados', 'Belgija', 'Belize',
        'Benin', 'Butan', 'Bjelorusija', 'Bolivija', 'Bosna i Hercegovina', 'Bocvana',
        'Brazil', 'Brunej', 'Bugarska',
        'Burkina Faso', 'Burundi', 'Crna Gora' ,
        'Čad', 'Češka', 'Čile', 'Cipar',
        'Demokratska Republika Kongo', 'Danska', 'Džibuti', 'Dominika', 'Dominikanska Republika',
        'Egipat', 'Ekvador', 'Ekvatrijalna Gvineja', 'Eritreja', 'Estonija', 'Etiopija',
        'Fidži', 'Filipini', 'Finska', 'Francuska',
        'Gabon', 'Gambija', 'Gana', 'Gvajana', 'Gvineja', 'Gvineja-Bisao',
        'Haiti', 'Honduras', 'Hrvatska',
        'Indija', 'Indonezija', 'Irak', 'Iran', 'Irska', 'Islanda', 'Italija', 'Izrael',
        'Jamajka', 'Japan', 'Jemen', 'Jordanija',
        'Kazahstan', 'Kambodža', 'Kamerun', 'Kanada', 'Kapverdski otoci', 'Katar', 'Kenija', 'Kirgistan',
        'Kiribati', 'Kina', 'Kipar', 'Koreja', 'Koreja (Sjevernakoreja)', 'Kostarika', 'Kuba',
        'Kuvajt',
        'Laos', 'Lesoto', 'Latvija', 'Liban', 'Liberija', 'Lihtenštajn', 'Litvanija', 'Luksemburg',
        'Libija',
        'Macao', 'Madagaskar', 'Makedonija', 'Malavi', 'Malezija', 'Mali', 'Malta', 'Malte',
        'Marooko', 'Maršalovi otoci', 'Mauricijus', 'Mauritanija', 'Mađarska', 'Mehiko',
        'Međunarodno područje', 'Mikronezija', 'Mjanmar', 'Moldavija', 'Monako', 'Mongolija',
        'Montserrat', 'Mozambik',
        'Namibija', 'Nauru', 'Nepal', 'Nemačka', 'Nikaragva', 'Niger', 'Nigerija', 'Niue',
        'Novozelandija', 'Norveška',
        'Oland', 'Oman',
        'Palestina', 'Panama', 'Papua Nova Gvineja', 'Paragvaj', 'Peruanski', 'Peru', 'Poljska',
        'Portoriko', 'Portugalia',
        'Reagionalni položaj',
        'Réunion', 'Ruanda', 'Rumunija', 'Rusija',
        'Sahara', 'Salvadorski', 'Samoa', 'Samota Amerika', 'San Marino', 'Sankt Kitts i Nevis',
        'Sankt Lucia', 'Sankt Vincent i Grenadini', 'Sao Tome i Principe', 'Sao Tome',
        'Saudijska Arabija', 'Sveta Jelena', 'Sjedinjene države', 'Seves Jelenski ostaci', 'Seničel',
        'Senegal', 'Sent Barts', 'Sent Kristof i Nevis', 'Sent Luska', 'Sent Marten',
        'Sent Pjer i Mikelon', 'Sent Vinsent i Grenadini', 'Septembar', 'Serf',
        'Severna Karolina', 'Severna Marijanaska ostrva', 'Severnajkoreja', 'Severnajmakedonija',
        'Sijalje', 'Sijam', 'Sijera Leone', 'Singapur', 'Sirija', 'Sjedinjene države',
        'Slovenija', 'Slovačka', 'Somali',  'Srbija' ,'Srednja Afrička Republika', 'Surija',
        'Svalbard i Jan Majen', 'Sveti Bartolomej', 'Sveti Marko', 'Sveti Marten',
        'Sveća Jelenina',
        'Šotska', 'Španjolska', 'Švedska', 'Švicarska',
        'Tajland', 'Tajvan', 'Tanzanija', 'Teritori Francuske Polinezije', 'Timor Leste',
        'Togo', 'Tokelau', 'Tonga', 'Trinidad i Tobago', 'Tunis', 'Turska',
        'Turkmenistan', 'Turcistan', 'Tuvalu',
        'Uganada', 'Ugarska', 'Ukrajina', 'Ujedinjene Arapske Emirate', 'Ujedinjeno Kraljevstvo',
        'Urugvaj', 'Uzbekistan',
        'Vanuatu', 'Vatikan', 'Velika Britanija', 'Velika Britanija i Irska', 'Velika Britanija i Severna Irska',
        'Velika Britanija i Sjeverna Irska', 'Velika Britanija i Sjedinjene države',
        'Vels', 'Venecuela', 'Vijetnam',
        'Virginija', 'Zapad Samare',
        'Zajednica nezavisnih država', 'Zambija', 'Zapadna Sahara', 'Zapadna Samarija',
        'Zelenortski Otoci', 'Zimbabve', 'Žambasija',
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
