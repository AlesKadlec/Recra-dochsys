# Uživatelský manuál – Docházka

Tento manuál je určený pro běžné uživatele systému docházky.
Je psaný jednoduše, krok za krokem.

## 1. Obecný úvod k systému
Docházkový systém RECRA je interní nástroj pro každodenní řízení personalistiky, směn, dopravy a náboru.  
Jednotlivé části jsou navázané na uživatelská oprávnění, takže každý uživatel vidí pouze agendy, které potřebuje ke své práci.

Systém typicky pokrývá tyto oblasti:
- **Docházka** – evidence přítomnosti, nepřítomností (např. DOV, DPN, ABS), ruční doplnění a úpravy záznamů.
- **Zaměstnanci** – přehled zaměstnanců, směnnost, nástupní místa, vazby na firmu a další provozní údaje.
- **Firmy a směny** – správa firem, objednávek a pravidel pro plánování směn.
- **Doprava** – vozový park, zastávky, časy nástupů a související výstupy pro svoz zaměstnanců.
- **Nábor** – evidence uchazečů, informace k nástupům a návazné náborové procesy.
- **Reporty a exporty** – provozní i manažerské přehledy, tisk a export dat pro další zpracování.
- **Uživatelé a oprávnění** – řízení přístupů podle role (běžný uživatel, koordinátor, vedení, admin).

Hlavní přínos systému je v tom, že sjednocuje personální a provozní data do jednoho prostředí, omezuje ruční přepisování a zrychluje denní operativu.

## 2. Přehled záložek v menu
Poznámka: některé záložky nebo podzáložky se zobrazují jen podle oprávnění uživatele.

### 2.1 Domů (`main.php`)
Co dělá:
- slouží jako hlavní vstupní obrazovka systému po přihlášení.
- podle role zobrazuje odlišný obsah (operativa řidiče, úkoly koordinace, rychlé odkazy).

Umožňuje:
- u role `Řidič` zobrazit přehled firem, rychlé vstupy do docházky pro směny `R/O/N`, změnu trasy a kontrolu DPN.
- u rolí `Koordinátor` a `Administrátor` pracovat s úkoly (nový úkol, stav `nedokončeno/rozpracováno/hotovo`).
- jako centrální rozcestník přecházet do všech dalších sekcí menu.

Přístup:
- všichni přihlášení uživatelé.

Nejčastější chyby:
- uživatel je přihlášen jiným účtem, než očekává.
- uživatel přehlédne, že obsah hlavní stránky je role-dependent (jiný pro řidiče, jiný pro koordinaci).

Doporučený postup:
- po přihlášení zkontrolovat vpravo v menu jméno uživatele a typ účtu.
- nejprve vyřešit operativní položky na hlavní stránce (úkoly, směnové vstupy) a až poté přejít do detailních agend.
- při nesouladu práv se odhlásit a přihlásit správným účtem.

Postup v režimu `Řidič` (odklik docházky):
1. Na `Domů` otevřete přehled firem a vyberte firmu, pro kterou budete dělat svoz.
2. Zkontrolujte, že máte vybranou dopravu (autobus/trasa):
   - pokud není vybraná, klikněte na `Změna trasy` (nebo `Není vybrána doprava`) a zvolte správné auto.
3. U firmy klikněte na směnu `R`, `O` nebo `N`.
4. Otevře se obrazovka docházky řidiče pro vybranou firmu + auto + směnu.
5. V tabulce zastávek klikněte u požadované zastávky na `Vyber`.
6. Teprve po výběru zastávky skenujte RFID kartu nebo zadejte osobní číslo do pole `Barcode nebo os. číslo`.
7. Systém po načtení:
   - zobrazí identifikaci zaměstnance,
   - zapíše docházku k vybrané zastávce a směně,
   - při neznámém kódu vypíše chybové hlášení.
8. Pokud není vybraná zastávka, systém zápis nepovolí a zobrazí upozornění `NENÍ VYBRÁNA ZASTÁVKA`.
9. Systém také hlídá okamžitou duplicitu (stejný zaměstnanec načtený hned znovu ve stejný den) a druhý zápis zablokuje.
10. Průběžně sledujte tabulku zastávek:
   - `Počet nastupujících` = plánovaný počet,
   - `V autobusu` = skutečně načtení,
   - zelený řádek = zastávka splněna, červený řádek = rozdíl.
11. Kliknutím na číslo v `Počet nastupujících` otevřete detailní modal zastávky s přehledem, kdo má nastoupit a kdo už byl načten.
12. Po dokončení směny můžete přes tlačítko `DPN` řešit i přidělené kontroly PN (`ZASTIŽEN / NEZASTIŽEN`).

Nejčastější chyby v režimu řidiče:
- odklik bez vybraného autobusu nebo bez vybrané zastávky.
- sken nesprávné karty / překlep v osobním čísle.
- dvojité rychlé načtení stejné osoby hned po sobě.
- práce ve špatné směně (`R/O/N`) nebo na špatné firmě.

Doporučený postup pro řidiče:
- před prvním odklikem vždy ověřit firmu, auto, směnu a vybranou zastávku.
- po každé zastávce porovnat `Počet nastupujících` vs. `V autobusu`.
- při chybě skenu ověřit osobní číslo ručně a teprve poté opakovat načtení.

### 2.2 Provoz
#### 2.2.1 Přehled zaměstnanců (`zamestnanci.php`)
Co dělá:
- zobrazuje centrální přehled zaměstnanců v tabulce s provozními, personálními a docházkovými údaji.
- na jednom místě propojuje údaje o zaměstnanci, směně, cílové stanici, dopravě a poslední docházce.
- podle zvoleného filtru pracovního poměru mění zobrazení sloupců (aktivní vs. ukončené/neaktivní záznamy).

Umožňuje:
- filtrovat data v horním panelu podle polí `Datum`, `Okruh dopravy`, `Pracovní poměr`, `Směna`, `Nepřítomnost`.
- použít tlačítka `Proveď výběr` a `Reset filtrů` pro rychlou práci s hlavním filtrem.
- používat rychlé sloupcové vyhledávání přímo v hlavičce tabulky (hledání pro každý sloupec zvlášť).
- exportovat výsledky přes tlačítka `Excel`, `PDF`, `Tisk`.
- upravit viditelnost sloupců přes tlačítko `Sloupce`.
- vyčistit sloupcové vyhledávání přes datatablicové tlačítko `Reset filtrů`.
- otevřít formulář `Nový záznam` (dle oprávnění stránky).

Možnost editace:
- uživatel s oprávněním vidí u řádku ikonu tužky a může otevřít modal editace zaměstnance.
- v editaci lze upravit zejména: příjmení, jméno, osobní číslo Premier, osobní číslo klient, adresu, email, RFID, telefon, zastávku, datum vstupu, datum výstupu, nepřítomnost, datum `DPN od`, firmu, cílovou stanici a směnnost.
- při nastavení nepřítomnosti systém naváže související logiku docházky (např. práce s DPN datem) a změny uloží do historie.

Detailní postup editace zaměstnance (krok za krokem):
1. V přehledu zaměstnanců nejprve nastavte filtry (datum, pracovní poměr, směna, nepřítomnost, cílová stanice), aby se zobrazil správný řádek.
2. U vybraného zaměstnance klikněte na ikonu tužky (otevře se modal `Editace zaměstnance`).
3. V části `Osobní údaje` upravte:
- `Příjmení`, `Jméno`, `Os. č. Premier`, `Os. č. klient`.
4. V části `Kontakt a přístup` upravte:
- `Adresa`, `Email`, `RFID`, `Telefon`, `Zastávka`.
5. V části `Pracovní stav` upravte:
- `Vstup`,
- `Výstup` (aktivuje se checkboxem; bez checkboxu zůstává výstup prázdný / nulový),
- `Nepřítomnost`,
- `DPN od` (vyplňovat jen pokud je nastavena odpovídající nepřítomnost).
6. V části `Zařazení` upravte:
- `Firma`,
- `Cílová stanice`,
- `Směnnost` (`3SM`, `3SM 8h`, `3SM 12h`, `4SM A/B/C/D` atd.).
7. Klikněte na `Uložit změnu`.
8. Po uložení systém provede návrat zpět do přehledu (do stejného režimu stránky) a zapíše akci do logu.

Kontrola po uložení:
- znovu vyhledejte zaměstnance stejným filtrem.
- ověřte kritická pole: směnnost, firma, cílová stanice, zastávka, nepřítomnost.
- pokud byla měněna nepřítomnost, zkontrolujte i návazný dopad v docházce/reportech.

Přístup:
- všichni přihlášení uživatelé (rozsah dat je omezen podle role a přiřazených firem).

Nejčastější chyby:
- kombinace filtrů vrátí prázdný výsledek, i když data existují.
- uživatel upraví záznam, ale neobnoví tabulku a neověří výsledek filtru.
- očekávání editace u účtu, který má pouze čtecí právo.

Doporučený postup:
- nejdřív nastavit horní filtry (`Pracovní poměr`, `Směna`, `Nepřítomnost`) a potom použít sloupcové filtry.
- pro tisk/reporting používat export až po kontrole, že je zapnutý správný filtr.
- po editaci vždy znovu vyhledat konkrétního zaměstnance a potvrdit uložené změny.

#### 2.2.2 Přehled firem (směny) (`firmy.php`)
Co dělá:
- zobrazuje centrální seznam firem pro operativní řízení směn.
- propojuje každou firmu s aktuálním počtem zaměstnanců a objednávkou (`zaměstnanců / objednávka`).
- funguje jako rychlý rozcestník pro řidičský režim docházky podle firmy a směny.

Umožňuje:
- přepínat mezi pohledem `Aktivní firmy` a `Všechny firmy`.
- zakládat novou firmu přes tlačítko `Nová firma`.
- editovat existující firmu přes tlačítko `Editace`.
- upravovat v modalu pole `Název firmy`, `Objednávka`, `Stav firmy (Aktivní/Neaktivní)`.
- sledovat samostatný řádek `Nepřiřazení zaměstnanci` (zaměstnanci bez firmy).
- u role `Řidič` přejít z řádku firmy rovnou do režimu nástupu (`R`, `O`, `N`) a na kontrolu `DPN`.
- podle role zobrazovat jen firmy, které má uživatel přiřazené (administrátor vidí všechny).

Detailní postup A: Provozní kontrola firem
1. Otevřete `2.2.2 Přehled firem`.
2. Pokud máte volbu filtrů, zvolte `Aktivní firmy` nebo `Všechny firmy` a potvrďte `Vyber`.
3. U každé firmy zkontrolujte:
- stav (`Aktivní` / `Neaktivní`),
- poměr `Zaměstnanců / Objednávka`,
- případně připravenost směnových tlačítek pro řidiče.
4. Zkontrolujte i řádek `Nepřiřazení zaměstnanci`, aby nezůstávaly osoby mimo firmu.

Detailní postup B: Založení nebo editace firmy
1. Klikněte na `Nová firma` nebo u existující firmy na `Editace`.
2. Vyplňte/zkontrolujte:
- `Název firmy`,
- `Objednávka`,
- `Stav firmy` (`Aktivní` / `Neaktivní`).
3. Uložte změnu (`Vytvořit firmu` nebo `Uložit změny`).
4. Po uložení ověřte, že se změna propsala do seznamu a navazujících agend.

Detailní postup C: Přechod řidiče do docházky ze seznamu firem
1. V řádku firmy použijte tlačítko směny `R`, `O` nebo `N`.
2. Systém otevře řidičský režim docházky pro vybranou firmu a směnu.
3. Tlačítko `DPN` slouží pro rychlou kontrolu počtu položek k řešení.
4. Pokud není vybraná doprava (autobus), nejdřív ji nastavte a až potom spouštějte směnu.

Přístup:
- menu podzáložka: `Koordinátor`, `Řidič`, `Teamleader`, `Administrátor`, `Náborář`.
- editace firem: `Koordinátor`, `Manažer dopravy`, `Administrátor`.
- přehled dat: `Administrátor` vidí všechny firmy, ostatní role jen své přiřazené firmy.

Nejčastější chyby:
- firma je založena jako neaktivní a uživatel ji pak nevidí při zapnutém filtru aktivních.
- nevyplněná nebo chybně nastavená objednávka zkreslí návazné plánování.
- uživatel edituje špatnou firmu při práci s podobnými názvy.
- opomenutí řádku `Nepřiřazení zaměstnanci`, který signalizuje nekompletní zařazení.
- řidič spouští směnu bez předchozího výběru dopravy.

Doporučený postup:
- před editací přepnout na `Všechny firmy`, pokud hledaná firma není v aktivním seznamu.
- u změn objednávek provést po uložení kontrolu poměru `zaměstnanců / objednávka`.
- po založení nové firmy zkontrolovat dostupnost v návazných agendách (`zamestnanci.php`, `dochazka.php`, řidičský režim).
- při práci řidiče vždy před směnou ověřit správnou firmu i vybrané vozidlo.

#### 2.2.3 Docházka (`dochazka.php`)
Co dělá:
- zobrazuje docházkové záznamy v denním i měsíčním režimu.
- sjednocuje přítomnost, nepřítomnosti, směnu, trasu, zastávku a poznámky na jednu tabulku.
- slouží jako hlavní kontrolní i opravný seznam docházky včetně ručních zásahů.

Umožňuje:
- filtrovat podle `dne`, `měsíce`, `směny` a `zakázky (cílová stanice)`.
- použít provozní varianty směn `R`, `O`, `N`, `R+O+N`, `R+O+N bez vlastní dopravy`, `Všechny`.
- u filtru `R+O+N bez vlastní dopravy` automaticky vyřadit záznamy s `Vlastní auto`.
- exportovat výsledky přes `Excel`, `PDF`, `Tisk`, měnit viditelnost přes `Sloupce`, čistit hledání přes `Reset filtrů`.
- u oprávněných rolí ručně vložit docházku přes modal `Vložení docházky`.
- u oprávněných rolí editovat nepřítomnost (`Přítomen`, `DPN`, `DOV`, `ABS`) nebo záznam kompletně smazat (`SMAZ`).
- v základním přehledu pracovat s posledními záznamy (limitovaný rozsah pro rychlost načtení).
- v režimu výpisu po filtru používat stránku primárně pro kontrolu a export; operativní editace se standardně dělá ze základního přehledu.

Důležité pravidlo filtrace:
- pokud je vyplněný měsíc i den současně, prioritu má filtr měsíce.

Detailní postup A: Denní a měsíční kontrola docházky
1. Otevřete `2.2.3 Docházka`.
2. Nastavte `Výběr dne` nebo `Výběr měsíce`.
3. Zvolte směnu (`R/O/N`, `R+O+N`, `R+O+N bez vlastní dopravy`, `Všechny`).
4. Volitelně nastavte filtr `Zakázka`.
5. Klikněte na `Proveď výběr`.
6. V tabulce kontrolujte sloupce `Datum`, `Čas`, `Směna`, `Trasa`, `Zastávka`, `Nepřítomnost`, `Pozn.`.

Detailní postup B: Manuální vložení docházky
1. V základním přehledu klikněte na `Vložení docházky` (dle oprávnění).
2. Vyberte zaměstnance a den (`dnes` nebo `včera`).
3. Potvrďte vložení.
4. Systém doplní směnu, zastávku, firmu, autobus i čas nástupu podle aktuálního nastavení zaměstnance.
5. Pokud už zaměstnanec záznam pro daný den má, vložení se neprovede.

Detailní postup C: Editace nebo smazání záznamu
1. V základním přehledu klikněte u řádku na `EDIT`.
2. V modalu zvolte novou hodnotu nepřítomnosti nebo `SMAZ`.
3. Uložte změnu.
4. Ověřte, že změna je viditelná v tabulce a odpovídá realitě.

Přístup:
- zobrazení docházky: všichni přihlášení uživatelé.
- ruční vložení a editace: `Koordinátor`, `Manažer dopravy`, `Administrátor`.
- zobrazená data jsou mimo administrátora omezená na přiřazené firmy uživatele.

Nejčastější chyby:
- současně vyplněný měsíc a den vede k nečekanému výběru (priorita je měsíční filtr).
- uživatel zapomene na aktivní filtr `zakázky` a vyhodnotí data jako chybějící.
- pokus o ruční vložení duplicitního záznamu pro stejný den.
- ruční vložení se udělá před kontrolou aktuální směny/zastávky u zaměstnance, takže vznikne formálně správný, ale provozně špatný záznam.
- uživatel očekává editaci v nesprávném kontextu stránky a přehlédne, že editace je určená pro oprávněné role.

Doporučený postup:
- pro denní kontrolu používat filtr `dne + směna`, pro uzávěrky používat filtr `měsíce`.
- před ručním vložením ověřit, že záznam pro daný den ještě neexistuje.
- před ručním vložením zkontrolovat u zaměstnance nastavenou směnu, firmu a nástupní místo.
- po editaci vždy zkontrolovat, že změna odpovídá tabulce a byla propsána správná nepřítomnost.
- před exportem vždy projít aktivní filtry a případně použít `Reset filtrů`.

#### 2.2.4 Report docházek (`report4.php`)
Co dělá:
- je klíčová provozní stránka pro plánování i kontrolu.
- na jednom místě kombinuje:
- týdenní plán směn a dopravy (`směna + trasa`),
- měsíční přehled docházky po dnech,
- nepřítomnosti, poznámky, nástupy/výstupy a stav přiřazení trasy.

Umožňuje:
- vybrat `cílovou stanici` a `měsíc` (včetně šipek vlevo/vpravo pro rychlý posun měsíců).
- vidět po řádcích zaměstnance a po sloupcích dny zvoleného měsíce.
- kliknout na jméno zaměstnance a otevřít detailní měsíční modal editace.
- v modalu nastavit pro každý týden:
- týdenní směnu (`R`, `O`, `N`, `X`),
- týdenní trasu (SPZ auta dostupná pro nástupní místo zaměstnance).
- v modalu nastavit pro jednotlivé dny:
- přítomnost / směnu (`R`, `O`, `N`),
- nepřítomnosti (`DPN`, `OČR`, `ABS`, `LEK`, `DOV`, `NAR`, `PRO`, `NEPV`, `NAHV`, `SVA`, `VOL`),
- poznámku ke dni (ikona tužky v buňce dne).
- uložit změny do databáze jedním tlačítkem.
- ukončit pracovní poměr přímo z řádku zaměstnance (tlačítko s ikonou dveří).
- vidět indikace:
- `⏱` osobní číslo + vstup/výstup,
- `🚌/🚗` typ dopravy (zastávka vs. vlastní auto),
- `📞` telefon,
- badge týdne (`R/O/N/X`) = aktuální týdenní směna,
- zelená kontrolka = týdenní trasa přiřazena, červená = chybí,
- `📝` v buňce dne = existuje poznámka,
- barevné zvýraznění dne podle typu záznamu.
- sledovat sumarizační tabulku pod hlavní maticí (počty `R/O/N` a nepřítomností po dnech).

Co je v `report4` nejdůležitější v praxi:
- zde se volí doprava a trasa na daný týden.
- zde je vidět, kdo kdy nastupuje a jakou má směnu v aktuálním týdnu.
- zde je vidět přehled docházky za zvolený měsíc po jednotlivých dnech.
- zde se řeší operativní změny (výpadky, náhrady, nepřítomnosti, posuny směn).

Detailní provozní workflow v `report4`:
1. Zvolte správnou `cílovou stanici`.
2. Zvolte měsíc, který chcete řídit/kontrolovat.
3. V seznamu zaměstnanců si najděte konkrétní osobu.
4. Podle ikonky směny/trasy ověřte, zda má v aktuálním týdnu přiřazenou správnou kombinaci.
5. Klikněte na jméno zaměstnance a otevřete modal kalendáře.
6. V levém sloupci řádku týdne nastavte:
- týdenní směnu,
- týdenní trasu (auto/SPZ).
7. V denních buňkách nastavte docházku nebo nepřítomnost pro konkrétní datum.
8. Doplňte poznámky tam, kde je potřeba předat kontext dalším rolím.
9. Uložte změny.
10. Po návratu zkontrolujte:
- barvy a hodnoty v měsíční matici,
- sumarizační řádky,
- ikonu přiřazení trasy (zelená/červená kontrolka).

Jak funguje zápis změn:
- při změně dne systém přepíše původní denní stav (docházka/nepřítomnost) na novou hodnotu.
- při změně týdne systém aktualizuje plán v tabulce `plan_smen` (směna + trasa).
- změny se logují pro audit.

Omezení speciální role (`Alliance-Leader`):
- nemůže měnit týdenní plán směn/tras.
- denně může upravovat jen omezený rozsah hodnot (typicky nepřítomnosti/N/A dle pravidel stránky).
- nemůže pracovat s poznámkami stejným způsobem jako plně oprávněné role.

Přístup:
- standardně v menu: `Koordinátor`, `Administrátor`, `Mzdový účetní`, vybraný `Teamleader` (`Alliance-Leader`).
- stránka navíc podporuje i `Manažer dopravy` při přímém otevření URL.

Nejčastější chyby:
- úprava zaměstnance ve špatné cílové stanici nebo měsíci.
- změna denní docházky bez navazující kontroly týdenní trasy.
- ignorování červené ikony chybějící trasy v aktuálním týdnu.
- nesprávná interpretace barev/symbolů v matici.
- uložení změn bez následné kontroly sumarizačních řádků.
- omezený účet (`Alliance-Leader`) se pokouší o nepovolené změny.

Doporučený postup:
- nejdřív potvrdit kontext: cílová stanice + měsíc.
- potom zkontrolovat týdenní plán (`směna/trasa`) u rizikových zaměstnanců.
- až následně dělat denní úpravy docházky.
- po každé větší dávce změn zkontrolovat souhrny pod tabulkou.
- červené indikace trasy řešit okamžitě, aby nenarušily nástupy.
- ukončení pracovního poměru dělat jen po ověření jména a data výstupu.

#### 2.2.5 Nastavení kalendářů (`report5.php`)
Co dělá:
- zobrazuje plánovací kalendáře pro čtyřsměnný provoz (`A/B/C/D`) po dnech vybraného měsíce.
- slouží pro nastavování směnového vzoru (`R`, `N`, `V`) u jednotlivých 4SM skupin.
- ukládá plán do tabulky kalendáře, ze které se následně promítá směna zaměstnanců 4SM.

Umožňuje:
- vybrat měsíc a přepínat mezi obdobími.
- pracovat se čtyřmi samostatnými kalendáři směn současně.
- nastavovat den po dni hodnoty `R`, `N`, `V` pro každou skupinu (`A/B/C/D`).
- ukládat změny okamžitě kliknutím na tlačítko dne (bez samostatného tlačítka `Uložit`).
- zvýraznit víkendy a rychle vizuálně kontrolovat rytmus směn.

Detailní postup A: Nastavení směnového vzoru
1. Otevřete `2.2.5 Nastavení kalendářů`.
2. Vyberte měsíc a potvrďte `Proveď výběr`.
3. U každé skupiny (`A/B/C/D`) nastavujte u jednotlivých dnů hodnoty:
- `R` (ranní),
- `N` (noční),
- `V` (volno).
4. Změna se zapíše ihned po kliknutí na hodnotu.
5. Po úpravě zkontrolujte návaznost mezi skupinami, aby nevznikly kolize kapacit.

Detailní postup B: Dopad do provozu
1. Hodnoty z `report5` se ukládají do směnného kalendáře 4SM.
2. Následná systémová synchronizace převádí plán do směn zaměstnanců 4SM.
3. Praktické mapování:
- kalendář `R` -> zaměstnanecká směna `R`,
- kalendář `N` -> zaměstnanecká směna `NN`,
- kalendář `V` -> zaměstnanecká směna `N/A`.
4. Při větších změnách vždy navazujte kontrolou v `2.2.4 Report docházek`.

Přístup:
- v menu standardně: `Koordinátor`, `Administrátor`, `Mzdový účetní` + vybraný `Teamleader` (`Alliance-Leader`).
- při přímém otevření URL: také `Manažer dopravy`.

Nejčastější chyby:
- změny se dělají v jiném měsíci, než uživatel očekává.
- uživatel zamění kalendář skupiny (`A/B/C/D`) a vytvoří chybné směnové schéma.
- uživatel čeká na tlačítko `Uložit`, i když změna je aplikovaná okamžitě.
- záměna významu `N` v kalendáři vs. `NN` v zaměstnanecké směně.
- změna je provedena jen v jedné skupině bez kontroly návaznosti ostatních.

Doporučený postup:
- před editací vždy ověřit vybraný měsíc i správnou skupinu kalendáře.
- změny dělat po menších blocích a průběžně kontrolovat týdenní rytmus všech skupin.
- po změnách provést kontrolu návaznosti mezi skupinami `A/B/C/D`.
- větší změny dělat mimo špičku operativy a následně potvrdit s provozem.
- finální stav ověřit v navazujícím plánování (`report4.php`), že odpovídá provozní realitě.

### 2.3 Nábor
#### 2.3.1 Evidence uchazečů CZ / PL (`nabory.php`)
Co dělá:
- je hlavní náborová databáze pro CZ/PL.
- eviduje kompletní životní cyklus uchazeče od prvního kontaktu po nástup nebo ukončení procesu.
- propojuje personální údaje, zdroj náboru, klienta, zodpovědné osoby a finální výsledek.

Umožňuje:
- vytvořit nový záznam přes tlačítko `Nový záznam`.
- upravit existující rozpracovaný záznam přes kartu uchazeče v pravém sloupci tabulky.
- vyplnit a spravovat identifikaci (`Příjmení`, `Jméno`, `Stát`, `Doklad`, `Datum narození`).
- vyplnit a spravovat kontakt (`Telefon`, `Adresa`).
- vyplnit a spravovat náborové informace (`Datum evidence`, `Zdroj inzerce`, `Pozice`, `Klient`, `Klient 2`, `Souhlas`, `Rekrutér`, `Koordinátor`, `Výsledek`).
- vyplnit a spravovat průběh spolupráce (`Nástup`, `Výstup`, `Důvod ukončení`).
- doplňovat interní poznámku.
- hlídat duplicity podle kombinace `Příjmení + Doklad` (online kontrola při psaní).
- filtrovat tabulku sloupcově (příjmení, jméno, telefon, klient, rekrutér, výsledek, poznámka).
- filtrovat tabulku datově (rozsah `Datum evidence`, samostatný filtr `Vstup` a `Výstup`).
- exportovat výstup přes `Excel`, `PDF`, `Tisk`.
- resetovat všechna hledání tlačítkem `Reset filtrů`.

Hlavní stavové hodnoty pole `Výsledek`:
- `Přijat`
- `Zamítnut`
- `Čeká se`
- `Nedostavil se`
- `Emergency`

Pravidlo editace v přehledu:
- u záznamů se stavem `Nastoupil` nebo `Zamítnut` se v tabulce standardně nenabízí tlačítko otevření karty pro další editaci.

Detailní postup založení nového uchazeče:
1. Klikněte na `Nový záznam`.
2. Vyplňte identifikační část včetně `Doklad` a `Datum narození`.
3. Vyplňte `Datum evidence`, `Zdroj inzerce`, `Pozice`, `Klient`, `Rekrutér`, `Koordinátor`.
4. Nastavte počáteční stav v poli `Výsledek` (obvykle `Čeká se`).
5. Sledujte hlášku duplicity; pokud systém vyhodnotí duplicitu (`Příjmení + Doklad`), zvýrazní pole a zablokuje uložení.
6. Po kontrole klikněte na `Vytvořit nábor`.
7. Záznam se uloží, zapíše do logu a objeví se v přehledu.

Detailní postup průběžné aktualizace uchazeče:
1. V tabulce najděte uchazeče pomocí sloupcových filtrů nebo datového rozsahu.
2. Otevřete kartu uchazeče.
3. Aktualizujte průběh: změna výsledku, doplnění nástupu/výstupu, doplnění důvodu ukončení, doplnění poznámky k dalším krokům.
4. Uložte změnu tlačítkem `Ulož změnu`.
5. Ověřte v tabulce, že se propsal nový stav a data.

Doporučené operativní workflow:
1. Každé ráno filtrovat podle `Datum evidence` a stavu `Čeká se`.
2. Po každém kontaktu ihned přepsat `Výsledek` a doplnit stručnou `Poznámku`.
3. Při přijetí uchazeče zkontrolovat, že je připraven pro navazující sekci `2.3.2 Informace k nástupům`.
4. Před týdenním reportem použít `Reset filtrů`, znovu nastavit relevantní filtr a exportovat.

Přístup:
- menu podzáložka: `Koordinátor`, `Administrátor`, `Náborář`, `Mzdový účetní`.
- vytváření a aktualizace záznamu: oprávněné náborové role (`Koordinátor`, `Manažer dopravy`, `Administrátor`, `Náborář`).
- role `Mzdový účetní` má typicky přístup zejména pro kontrolu/přehled dat.

Nejčastější chyby:
- založení duplicitního uchazeče bez kontroly `Doklad`.
- neaktualizovaný stav `Výsledek` po kontaktu s uchazečem.
- chybějící `Rekrutér` nebo `Koordinátor` komplikuje odpovědnost za další krok.
- nevyplněný `Důvod ukončení` u ukončeného procesu.
- export nad špatně nastaveným filtrem.

Doporučený postup:
- před uložením vždy ověřit, že neexistuje duplicitní kombinace `Příjmení + Doklad`.
- průběžně udržovat aktuální `Výsledek`, `Poznámku` a návazná data.
- při přechodu do nástupu ověřit kompletnost údajů pro sekci `Informace k nástupům`.
- před exportem vždy resetovat filtry a vědomě nastavit nový výběr.

#### 2.3.2 Informace k nástupům (`informace.php`)
Co dělá:
- zobrazuje přijaté uchazeče připravené k nástupu a jejich onboarding stav.
- propojuje nábor s provozními údaji potřebnými před nástupem.

Umožňuje:
- evidovat doplňující nástupní informace: obuv, oblečení, telefonní kontakt, datum nástupu, směna, nástupní místo, firma, cílová stanice, osobní číslo.
- otevřít detailní modal `EDIT` pro doplnění nástupních dat.
- použít akci `Nástup` (u oprávněných rolí) pro převod do zaměstnanecké agendy.
- rychle vizuálně kontrolovat připravenost přes ikonky a doplňující badge.

Přístup:
- `Koordinátor`, `Administrátor`, `Náborář`.

Nejčastější chyby:
- uchazeč má stav `Přijat`, ale chybí povinná nástupní data.
- není vyplněná směna/nástupní místo, takže navazující provoz nemá kompletní podklad.
- `Nástup` je proveden bez finální kontroly údajů.

Doporučený postup:
- před akcí `Nástup` vždy dokončit nástupní checklist v modalu.
- ověřit správnost firmy, cílové stanice a směny.
- po potvrzení nástupu zkontrolovat, že se zaměstnanec objevil v provozní agendě.

### 2.4 Doprava
#### 2.4.1 Vozový park (`vozovypark.php`)
Co dělá:
- spravuje databázi vozidel, zastávek a časů tras pro směny.
- je hlavní konfigurační vrstva dopravy pro návazné docházkové procesy.

Umožňuje:
- upravovat vozidla (`SPZ`, označení) přes modal `AUTO`.
- spravovat zastávky a časy jednotlivých tras (`R`, `O`, `N`) přes modal `ZASTÁVKY`.
- přidat existující zastávku do konkrétní trasy auta.
- odebrat zastávku jen z konkrétní trasy auta (bez globálního smazání zastávky).
- přidat novou zastávku globálně do seznamu všech zastávek.
- smazat zastávku globálně ze systému včetně návazných vazeb.
- při mazání zastávky automaticky vyčistit navazující data (`trasy`, `nastupy`, reset `nastup` u zaměstnanců).
- u administrátora využít `Přehled zastávek` pro rychlý audit.

Rozlišení dvou režimů práce se zastávkami:
- režim `ZASTÁVKY` u konkrétního auta: řeší přiřazení zastávek a časy pro jednu trasu.
- režim `Přehled zastávek`: řeší globální seznam zastávek pro celý systém.

Detailní postup A: Editace zastávek v trase konkrétního auta (`ZASTÁVKY`)
1. Otevřete `2.4.1 Vozový park`.
2. U vybraného auta klikněte na tlačítko `ZASTÁVKY`.
3. V modalu uvidíte seznam zastávek přiřazených k danému autu.
4. U každé zastávky upravte časy:
- `R` (ranní),
- `O` (odpolední),
- `N` (noční).
5. Uložte změny tlačítkem `Uložit změny`.
6. Pokud chcete zastávku odebrat jen z této trasy, použijte u řádku tlačítko `X` (potvrzení).
7. Pokud chcete do trasy přidat další zastávku:
- vyberte ji v poli `Vyber zastávku k přidání`,
- klikněte na `+ Přidat zastávku`,
- po přidání nastavte její časy `R/O/N` (nový řádek se zakládá s časem `00:00`).

Detailní postup B: Přidání nové zastávky do systému (`Přehled zastávek`)
1. Otevřete `Přehled zastávek` (typicky role `Administrátor`).
2. Do pole `Nová zastávka` zadejte název.
3. Klikněte na `Přidat`.
4. Nová zastávka je vytvořena globálně, ale ještě není přiřazena ke konkrétní trase auta.
5. Přepněte se zpět do modalu `ZASTÁVKY` u požadovaného auta a zastávku do trasy přiřaďte.

Detailní postup C: Globální smazání zastávky (`Přehled zastávek`)
1. V tabulce zastávek klikněte u zastávky na `Smazat`.
2. Potvrďte varování.
3. Systém provede kaskádové změny:
- odstraní zastávku ze všech tras,
- u zaměstnanců, kteří ji měli přiřazenou, nastaví `nastup = 0` (nepřiřazen),
- smaže zastávku ze seznamu `nastupy`.
4. Po smazání vždy zkontrolujte dotčené zaměstnance a přiřaďte jim nové nástupní místo.

Přístup:
- `Koordinátor`, `Manažer dopravy`, `Administrátor`.
- tlačítko `Přehled zastávek` (globální přidání/smazání) je standardně dostupné především pro `Administrátor`.

Nejčastější chyby:
- úprava času jen u jedné směny bez kontroly ostatních (`R/O/N`).
- záměna akcí `odebrat z trasy` vs. `globálně smazat zastávku`.
- přidání zastávky do trasy bez následného nastavení časů (zůstane `00:00`).
- smazání zastávky bez ověření, koho se změna dotkne.
- záměna vozidel při podobném označení.

Doporučený postup:
- změny tras dělat po schválení s provozem a mimo okamžitý nástup směny.
- po každé změně zastávky v trase zkontrolovat časy `R/O/N` a potvrdit výsledek v `2.4.2 Report dopravy`.
- novou globální zastávku vždy následně přiřadit do relevantních tras.
- před globálním smazáním zastávky ověřit seznam dotčených zaměstnanců a připravit náhradní nástupní místo.

#### 2.4.2 Report dopravy (`report2.php`)
Co dělá:
- je centrální týdenní kontrolní přehled dopravy podle směny a přiřazených tras.
- zobrazuje, kdo má pro daný týden přiřazený autobus/trasy a kdo zůstává bez přiřazení.
- slouží jako operativní kontrola před nástupy směn i při týdních změnách plánu.

Umožňuje:
- přepínat týden pomocí šipek a vracet se na `Aktuální týden`.
- přepínat směnové pohledy (`R`, `O`, `N`, `X`, `CH`).
- v režimu `R/O/N/X` zobrazit zaměstnance naplánované v `plan_smen` pro vybranou směnu.
- v režimu `CH` zobrazit aktivní zaměstnance bez přiřazené trasy v daném týdnu (chybějící řádek v plánu nebo `trasa = 0`).
- vyhledávat po sloupcích (`jméno`, `firma`, `cílová`, `zastávka`, `trasa`).
- používat globální vyhledávání DataTables (pole vpravo nahoře).
- exportovat přes `Excel`, `PDF`, `Tisk`, měnit viditelnost přes `Sloupce`, čistit přes `Reset filtrů`.
- řadit záznamy klikem na hlavičku sloupce (výchozí řazení je podle příjmení).

Význam jednotlivých pohledů směn:
- `R`, `O`, `N`: standardní směny pro denní operativní kontrolu rozvozu.
- `X`: speciální/plánovací směna vedená v `plan_smen` (mimo standardní trojici).
- `CH`: kontrolní pohled na chybějící přiřazení dopravy (nejde o směnu docházky).

Detailní postup A: Běžná kontrola dopravy (R/O/N/X)
1. Otevřete `2.4.2 Report dopravy`.
2. Zkontrolujte nadpis `PLÁN DOPRAVY na týden xx/yyyy`.
3. Tlačítky `◀`/`▶` nastavte správný týden, případně použijte `Aktuální týden`.
4. Vyberte směnu (`R`, `O`, `N` nebo `X`).
5. Pomocí filtrů v druhém řádku tabulky zužte seznam podle firmy, cílové, zastávky nebo trasy.
6. Ověřte, že u zaměstnanců jsou správně vyplněné sloupce `Zastávka` a `Trasa`.
7. Pokud potřebujete podklad pro dispečink nebo vedení, použijte export `Excel`/`PDF` nebo `Tisk`.

Detailní postup B: Kontrola chybějících tras (`CH`)
1. Přepněte na tlačítko `CH`.
2. Zobrazí se zaměstnanci, kteří jsou aktivní v daném týdnu, ale nemají přiřazenou trasu.
3. Vyfiltrujte firmu nebo cílovou stanici pro rychlé předání odpovědné osobě.
4. Přiřazení trasy opravte v plánování směn nebo v navazující dopravní konfiguraci.
5. Vraťte se do `CH` a ověřte, že zaměstnanec po opravě ze seznamu zmizel.

Detailní postup C: Práce s filtry a exporty
1. Sloupcové filtry používejte pro přesné hledání (např. konkrétní zastávka nebo SPZ).
2. Globální vyhledávání používejte pro rychlé fulltextové dohledání osoby/firmy.
3. Pokud tabulka vrací nečekaně málo dat, spusťte `Reset filtrů`.
4. Před exportem zkontrolujte, že máte správně nastavený týden i směnu.
5. U PDF je výstup v rozložení `A4 na šířku` pro lepší čitelnost.

Přístup:
- v menu: `Koordinátor`, `Řidič`, `Teamleader`, `Administrátor`.
- při přímém otevření URL má přístup i role `Manažer dopravy`.

Nejčastější chyby:
- práce ve špatném týdnu (zejména při přechodu roku).
- záměna pohledů `X` a `CH` (mají odlišný význam).
- uživatel považuje `CH` za směnu, ale jde o kontrolní pohled bez přiřazené trasy.
- aktivní filtr ve sloupci nebo v globálním vyhledávání zkreslí počet zobrazených řádků.
- práce s exportem bez předchozí kontroly nastaveného týdne/směny.
- očekávání přímé editace v reportu (stránka je primárně kontrolní/reportovací).

Doporučený postup:
- před každou kontrolou vždy potvrdit týden/rok v nadpisu.
- kontrolu dělat ve stejném pořadí: `R` -> `O` -> `N` -> `X` -> `CH`.
- po úpravách v dopravě znovu otevřít `CH` a ověřit odstranění chybějících přiřazení.
- při operativních změnách sdílet export (`Excel`/`PDF`) až po `Reset filtrů` a finální kontrole.
- při opakovaném výskytu chybějících tras eskalovat na správce plánování směn.

### 2.5 Reporty
#### 2.5.1 Report pro vedení (`report3.php`)
Co dělá:
- vytváří měsíční manažerský přehled obsazenosti, nepřítomností, hodin a odhadovaných tržeb.
- kombinuje data z docházky, zaměstnaneckých cílových stanic a nepřítomností do jednoho přehledu.
- odděluje dopravu na `vlastní doprava` a `dopravce` pro rychlé porovnání provozního zatížení.
- počítá finanční odhad bez DPH podle zadané `fakturace v Kč/hod`.

Umožňuje:
- vybrat měsíc a zadat/aktualizovat `fakturaci v Kč/hod`.
- sledovat denní i měsíční součty po cílových stanicích.
- vyhodnotit samostatně vlastní dopravu vs. externí dopravce.
- sledovat souhrnné řádky: denní počet zaměstnanců, nepřítomní, odpracované hodiny, denní tržba bez DPH, ztráta zaměstnanců a ztráta tržby.
- mít v tabulce fixovaný levý sloupec a hlavičky (lepší orientace při širokém měsíčním pohledu).
- vytisknout výstup v print-friendly formátu (horní panel se při tisku skrývá).

Detailní postup A: Výběr období a nastavení fakturace
1. Otevřete `2.5.1 Report pro vedení`.
2. V poli `Výběr měsíce` zvolte požadovaný měsíc (`YYYY-MM`).
3. Do pole `Fakturace v Kč/hod` zadejte aktuální hodinovou sazbu pro report.
4. Klikněte na `Proveď výběr`.
5. Systém načte data za zvolený měsíc a uloží/aktualizuje fakturaci pro dané období.

Detailní postup B: Čtení hlavní tabulky
1. Horní řádek obsahuje čísla dnů v měsíci, druhý řádek zkratky dnů (`Po`, `Út`, `St`, `Čt`, `Pá`, `So`, `Ne`).
2. Každý řádek `cílové stanice` ukazuje denní počet zaměstnanců a měsíční součet ve sloupci `Suma`.
3. Pod cílovými stanicemi následují řádky nepřítomností podle typu (`D`, `OČR`, `DOV`, apod. dle dat).
4. V dolní části jsou agregované řádky pro vedení:
- `Denní počet zaměstnanců – vlastní doprava`,
- `Denní počet zaměstnanců – dopravce`,
- `Denní počet zaměstnanců – celkem`,
- `Denní počet nepřítomných`,
- `Denní celkový počet hodin`,
- `Denní tržba bez DPH`,
- `Ztráta zaměstnanců za den`,
- `Ztráta denní tržby bez DPH`.

Detailní postup C: Jak jsou počítány klíčové metriky
1. `Denní celkový počet hodin` = denní obsazenost x `7,5 hod`.
2. `Denní tržba bez DPH` = `(denní obsazenost x 7,5 x fakturace) / 1000`.
3. Měsíční `Suma` u tržeb je tedy uvedena v `tisících Kč bez DPH`.
4. Rozdělení dopravy:
- `vlastní doprava` je interně vedena přes vlastní autobus,
- `dopravce` zahrnuje externí dopravu a záznamy mimo vlastní autobus.

Detailní postup D: Tisk a podklady pro porady
1. Po kontrole dat použijte standardní tisk prohlížeče.
2. Systém při tisku schová ovládací panel a ponechá čistý tabulkový výstup.
3. Tisk používejte jako podklad pro měsíční porady vedení a controlling.

Přístup:
- `Administrátor`.

Nejčastější chyby:
- špatně nastavená fakturace v Kč/hod zkreslí celý měsíční odhad.
- porovnání různých měsíců bez sjednocení fakturace (metodicky neporovnatelné výsledky).
- přehlédnutí, že tržby jsou uváděny v `tisících Kč bez DPH`.
- interpretace řádků ztrát bez ověření interního nastavení objednávkového základu.
- mylné očekávání exportních tlačítek `Excel/PDF` (sekce je primárně tiskový manažerský report).

Doporučený postup:
- před analýzou ověřit správnou hodnotu fakturace a vybraný měsíc.
- nejprve vyhodnotit obsazenost podle cílových stanic a nepřítomnosti, až potom finanční řádky.
- při porovnání měsíců používat jednotnou metodiku (stejná logika fakturace a stejný výklad metrik).
- po kontrole vytisknout finální verzi a použít ji jako podklad pro porady vedení.

#### 2.5.2 Logy (`logs.php`)
Co dělá:
- zobrazuje historii změn v systému jako auditní stopu.
- pomáhá dohledat původ zásahu při řešení chyb nebo reklamací dat.

Umožňuje:
- prohlížet posledních 500 log záznamů od nejnovějších.
- vidět `uživatele`, `typ operace`, `popis změny` a `datum/čas`.
- rozeznat systémové operace (`uživatel = system`) od ručních zásahů.

Přístup:
- `Administrátor`.

Nejčastější chyby:
- uživatel očekává přístup do reportů, ale jeho role jej nemá.
- při kontrole problému se neověří přesný čas události a zamění se podobné záznamy.

Doporučený postup:
- při incidentu nejprve zapsat časový rámec a jméno dotčeného uživatele/zaměstnance.
- pak v logu dohledat odpovídající operaci a až následně provádět opravy dat.
- logy používat i preventivně při kontrole citlivých změn.

### 2.6 Zaměstnanci
#### 2.6.1 Hodinové sazby (`sazby.php`)
Co dělá:
- eviduje hodinové sazby zaměstnanců včetně časové platnosti.
- umožňuje držet historii změn sazeb v čase.

Umožňuje:
- založit novou sazbu přes tlačítko `Nová sazba`.
- upravit existující sazbu přes tlačítko `E` (editace řádku).
- pracovat s poli `zaměstnanec`, `sazba`, `platnost od`, `platnost do`, `poznámka`.
- mít aktivní sazbu i bez koncového data (`platnost do` prázdná).

Přístup:
- `Administrátor`, `Mzdový účetní`.

Nejčastější chyby:
- překryv období platnosti u stejného zaměstnance.
- chybně zapsaná sazba (desetinná čárka/tečka nebo jednotky).
- opomenutí vyplnit poznámku u mimořádné změny.

Doporučený postup:
- před uložením nové sazby ověřit, že časově navazuje na předchozí období.
- používat jednotný zápis sazeb a vždy kontrolovat výsledný řádek v tabulce.
- u mimořádných sazeb doplnit důvod do poznámky kvůli auditní dohledatelnosti.

#### 2.6.2 XML převody (`xml.php`)
Co dělá:
- převádí docházkové XML do formátu pro systém Premier.
- mapuje zákaznická osobní čísla na Premier ID podle interních dat zaměstnanců.

Umožňuje:
- nahrát vstupní `.xml` soubor (max 10 MB).
- před exportem vypsat nepřeložená osobní čísla.
- vygenerovat export, kde se upraví `osobni_cislo`, `cislo_pracovniho_pomeru` a normalizuje `pracovni_pomer`.
- stáhnout exportní XML soubor připravený ke zpracování.
- zapnout debug režim (`?debug=1`) pro detailní troubleshooting.

Přístup:
- stránka podporuje role `Administrátor`, `Koordinátor`, `Manažer dopravy`.

Nejčastější chyby:
- nahrán není XML soubor, ale jiný formát.
- chybí mapování osobních čísel, proto část záznamů nelze přeložit.
- uživatel neprovede finální kontrolu seznamu nepřeložených hodnot.

Doporučený postup:
- po uploadu vždy projít hlášku o nepřeložených osobních číslech.
- chybějící mapování doplnit v zaměstnaneckých datech a export zopakovat.
- exportní soubor stáhnout a archivovat spolu s měsícem/obdobím.

Kdo má přístup:
- `Hodinové sazby`: `Administrátor`, `Mzdový účetní`.
- `XML převody`: `Administrátor`, `Koordinátor`, `Manažer dopravy`.

### 2.7 Uživatelé (`uzivatele.php`)
Co dělá:
- spravuje uživatelské účty, role, aktivitu účtu a přiřazení firem/dopravy.
- zajišťuje, aby každý účet měl správná oprávnění pro konkrétní agendu.

Umožňuje:
- zobrazit přehled účtů včetně role, přiřazené firmy, autobusu a stavu `aktivní/neaktivní`.
- založit nový účet (`Administrátor`) s parametry login, heslo, role, firma, autobus, e-mail.
- editovat existující účet (role, firmy, autobus, aktivita, e-mail).
- při editaci přiřadit více firem přes multi-select.
- měnit hesla přes samostatný formulář se dvojím potvrzením.

Přístup:
- menu vstup: `Koordinátor`, `Manažer dopravy`, `Administrátor`.
- založení nového účtu: `Administrátor`.
- editace uživatele a změna hesla: `Administrátor`; `Koordinátor` dle nastavení zejména u účtů řidičů.

Nejčastější chyby:
- špatně přiřazená role způsobí chybějící nebo naopak nadbytečné menu.
- zapomenuté přiřazení firmy zablokuje práci v provozních tabulkách.
- heslo je změněno bez ověření přihlášení daného uživatele.

Doporučený postup:
- po každé změně účtu provést rychlou kontrolu práv po novém přihlášení.
- u role vždy zároveň zkontrolovat přiřazené firmy a autobus.
- nepoužívat sdílené účty, každý uživatel má mít vlastní identitu v logách.

### 2.8 Odhlásit (`odhlasit.php`)
Co dělá:
- bezpečně ukončuje uživatelskou relaci.

Umožňuje:
- odhlásit účet jedním kliknutím z hlavního menu.
- předejít nechtěnému použití účtu na sdíleném zařízení.

Přístup:
- všichni přihlášení uživatelé.

Nejčastější chyby:
- zavření karty/prohlížeče bez kliknutí na `Odhlásit`.
- ponechání aktivní relace na směnovém nebo sdíleném PC.

Doporučený postup:
- vždy končit práci přes `Odhlásit`.
- po odhlášení ověřit zobrazení přihlašovací stránky.
- na sdíleném zařízení zavřít i prohlížeč.

### 2.9 Informace o přihlášeném uživateli (pravá část menu)
Co dělá:
- zobrazuje aktuálně přihlášené jméno a text role (např. `Řidič`, `Koordinátor`, `Administrátor`).

Umožňuje:
- rychlou kontrolu, že uživatel pracuje pod správným účtem.
- jednodušší diagnostiku problémů s právy a viditelností menu.

Přístup:
- všichni přihlášení uživatelé.

Nejčastější chyby:
- uživatel přehlédne, že je přihlášen pod jiným účtem.
- očekává funkce, které patří jiné roli.

Doporučený postup:
- před každou citlivou operací zkontrolovat jméno a roli vpravo nahoře.
- při hlášení incidentu vždy uvést i název role, pod kterou problém vznikl.
- při nesouladu role se okamžitě odhlásit a přihlásit správným účtem.

---

## 3. K čemu stránka slouží
Stránka **Docházka** slouží k tomu, abyste:
- viděli přehled docházky zaměstnanců,
- našli záznamy podle dne, měsíce, směny nebo zakázky,
- vytiskli nebo exportovali přehled (Excel, PDF),
- u vybraných uživatelů také ručně vložili nebo upravili docházku.

`[VLOŽIT SCREENSHOT: Hlavní obrazovka Docházka]`

---

## 4. Co na stránce najdete
Po otevření stránky uvidíte:
- filtr (den, směna, zakázka, měsíc),
- tlačítko **Proveď výběr**,
- tabulku s docházkou,
- (u oprávněných uživatelů) tlačítko **Vložení docházky**,
- v tabulce (u oprávněných uživatelů) tlačítko **EDIT**.

`[VLOŽIT SCREENSHOT: Filtr + tabulka]`

---

## 5. Jak filtrovat docházku
### 5.1 Filtr podle dne
1. V poli **Výběr dne** zvolte datum.
2. Vyberte směnu a případně zakázku.
3. Klikněte na **Proveď výběr**.

`[VLOŽIT SCREENSHOT: Výběr dne + Proveď výběr]`

### 5.2 Filtr podle měsíce
1. V poli **Výběr měsíce** zvolte měsíc.
2. Klikněte na **Proveď výběr**.

Poznámka: Pokud je vyplněný měsíc, zobrazí se záznamy za celý měsíc.

`[VLOŽIT SCREENSHOT: Výběr měsíce]`

### 5.3 Význam směn
- **R** = ranní
- **O** = odpolední
- **N** = noční
- **R+O+N** = všechny tyto směny
- **Bez vlastní dopravy** = záznamy bez vlastní dopravy
- **Všechny směny** = bez omezení směny

---

## 6. Jak pracovat s tabulkou
V tabulce můžete:
- vyhledávat přes pole hledání,
- měnit zobrazené sloupce,
- exportovat data,
- tisknout.

Nad tabulkou najdete tlačítka:
- **Excel**
- **PDF**
- **Tisk**
- **Sloupce**
- **Reset filtrů**

`[VLOŽIT SCREENSHOT: Tlačítka nad tabulkou]`

---

## 7. Ruční vložení docházky (jen oprávnění uživatelé)
Pokud máte oprávnění, uvidíte tlačítko **Vložení docházky**.

Postup:
1. Klikněte na **Vložení docházky**.
2. Vyberte zaměstnance.
3. Vyberte den (dnes / včera).
4. Potvrďte tlačítkem **Vložit docházkový záznam**.

Systém automaticky doplní směnu, nástupní místo a čas podle nastavení zaměstnance.

`[VLOŽIT SCREENSHOT: Modal Vložení docházky]`

Pokud už záznam pro daný den existuje, systém vložení nepovolí a zobrazí hlášku.

---

## 8. Úprava docházky (jen oprávnění uživatelé)
V pravé části řádku tabulky je tlačítko **EDIT**.

Postup:
1. Klikněte u vybraného řádku na **EDIT**.
2. V poli **Nepřítomnost** zvolte požadovanou hodnotu.
3. Klikněte na **Ulož změnu**.

Možné hodnoty:
- **Přítomen**
- **DPN**
- **DOV**
- **ABS**
- **Záznam úplně smazat**

`[VLOŽIT SCREENSHOT: Editace záznamu]`

---

## 9. Nejčastější situace
### 9.1 „Nevidím tlačítko Vložení docházky“
Nemáte oprávnění pro ruční vkládání. Obraťte se na správce.

### 9.2 „Nevidím tlačítko EDIT“
Nemáte oprávnění pro editaci záznamů.

### 9.3 „Nenašel jsem žádná data“
Zkontrolujte:
- správné datum nebo měsíc,
- zvolenou směnu,
- zakázku (zkuste hodnotu **Vše**).

### 9.4 „Nejde vložit docházka“
Pravděpodobně už pro zaměstnance existuje záznam v daný den.

---

## 10. Doporučený denní postup
1. Otevřít **Docházka**.
2. Nastavit filtr (dnes + požadovaná směna).
3. Zkontrolovat přehled.
4. Dle potřeby doplnit chybějící záznamy.
5. Dle potřeby upravit nepřítomnosti.
6. Exportovat nebo vytisknout výstup.

---

## 11. Místa pro doplnění screenshotů
- [ ] Hlavní obrazovka Docházka
- [ ] Filtrační panel
- [ ] Výsledná tabulka
- [ ] Tlačítka exportu
- [ ] Vložení docházky (modal)
- [ ] Editace docházky (modal)
- [ ] Ukázka chybové hlášky při duplicitě

---

## 12. Kontakty (doplňte)
- Správce aplikace:
- IT podpora:
- Telefon / e-mail:
