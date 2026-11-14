<?php

declare(strict_types=1);

$l = &$GLOBALS['TL_LANG']['tl_neo_websitecheck_config'];

$l['title_legend'] = 'Configurazione';
$l['mail_legend'] = 'E-mail al visitatore';
$l['mail_design_legend'] = 'Design dell’e-mail';
$l['pdf_legend'] = 'Report PDF';
$l['pdf_design_legend'] = 'Design del report PDF';
$l['owner_legend'] = 'La Sua copia';
$l['element_legend'] = 'L’elemento sulla pagina';
$l['fe_result'] = ['Risultato sulla pagina', 'Breve: voto, una frase breve e il rimando al PDF. Completo: in più punteggi, tutte le aree, misure e punti di forza. Il PDF contiene sempre tutto.'];
$l['fe_result_options'] = ['compact' => 'Breve (consigliato)', 'full' => 'Completo'];
$l['expert_legend'] = 'Copia interna come template Twig';

$l['title'] = ['Nome', 'Nome interno, ad es. «Italiano» o «Inglese – hotel».'];
$l['language'] = ['Lingua', 'Un elemento senza assegnazione esplicita usa la configurazione la cui lingua corrisponde alla sua pagina.'];
$l['mail_subject'] = ['Oggetto', 'Segnaposto ammessi. Predefinito: «Il Suo check del sito web per {{domain}}: voto {{grade}} · {{grade_word}}»'];
$l['mail_from_name'] = ['Nome del mittente', 'Vuoto = impostazioni / nome dell’azienda.'];
$l['mail_from_email'] = ['Indirizzo del mittente', 'Vuoto = impostazioni / config.yaml / indirizzo dell’amministratore Contao.'];
$l['mail_reply_to'] = ['Indirizzo per le risposte', 'Vuoto = impostazioni.'];
$l['mail_owner_subject'] = ['Oggetto della Sua copia', 'Predefinito: «[Check del sito web] {{domain}} · voto {{grade}} · {{firstname}}»'];
$l['mail_design'] = ['E-mail', 'Aggiunga blocchi a sinistra, li clicchi nell’anteprima e li modifichi a destra. «Carica modello» imposta una struttura già pronta.'];
$l['pdf_filename'] = ['Nome del file PDF', 'Con segnaposto, ad es. website-check-{{domain}}-{{date}}.pdf (funzionano anche {{grade}}, {{firstname}}, {{lastname}}, {{company}}).'];
$l['pdf_design'] = ['Report PDF', 'Componga pagina per pagina: copertina, blocchi di dati, testi e immagini propri, interruzioni di pagina. L’anteprima usa l’ultimo check completato oppure dati dimostrativi.'];
$l['mail_body'] = ['E-mail come codice Twig (modalità avanzata)', 'Solo per amministratori. Si modifica nel designer tramite «Avanzata (Twig)». Se compilato, questo codice viene usato al posto del design visuale.'];
$l['owner_body'] = ['La Sua copia come template Twig', 'Vuoto = template incluso nel modulo.'];
$l['pdf_body'] = ['Report PDF come codice Twig (modalità avanzata)', 'Solo per amministratori. Si modifica nel designer tramite «Avanzata (Twig)». Se compilato, questo codice viene usato al posto del design visuale.'];

$l['fe_url_label'] = ['Etichetta sopra il campo dell’indirizzo', 'Predefinito: «Indirizzo del Suo sito web»'];
$l['fe_submit'] = ['Pulsante «Controlla ora»', ''];
$l['fe_gate_title'] = ['Modulo di contatto: titolo', 'Predefinito: «Il Suo risultato è pronto»'];
$l['fe_gate_text'] = ['Modulo di contatto: testo', 'Predefinito: «Dove possiamo inviarlo? …»'];
$l['fe_gate_legal'] = ['Modulo di contatto: nota sulla privacy', 'Il link alla pagina della privacy viene aggiunto automaticamente.'];
$l['fe_newsletter'] = ['Testo della casella newsletter', ''];
$l['fe_gate_submit'] = ['Modulo di contatto: pulsante', 'Predefinito: «Mostra il risultato»'];
$l['fe_disclaimer'] = ['Risultato: nota', 'Predefinito: «Valutazione automatica basata su …»'];
$l['fe_cta'] = ['Risultato: pulsante', 'Predefinito: «Parliamo del risultato — gratuitamente»'];

$l['new'] = ['Nuova configurazione', 'Aggiunga una configurazione per un’altra lingua o un altro utilizzo'];
$l['edit'] = ['Modifica', 'Modifica la configurazione ID %s'];
$l['copy'] = ['Duplica', 'Duplica la configurazione ID %s'];
$l['delete'] = ['Elimina', 'Elimina la configurazione ID %s'];
$l['show'] = ['Dettagli', 'Mostra i dettagli dell’ID %s'];
$l['preview_pdf'] = ['Anteprima PDF', 'Apre il report progettato come PDF'];
$l['preview_mail'] = ['Anteprima e-mail', 'Apre l’e-mail progettata nel browser'];
$l['automatic'] = 'Automatica (in base alla lingua della pagina)';
$l['element_config'] = ['Configurazione', 'Quale configurazione del check (testi, e-mail, PDF) usa questo elemento. Automatica = quella corrispondente alla lingua di questa pagina.'];

// Multilingual sites: links and newsletter per configuration (= per language); empty = global value.
$l['links_legend'] = 'Pagine di questa lingua';
$l['contact_page'] = ['Pagina di contatto', 'Destinazione di «Parliamo del risultato» nel modulo, nell’e-mail e nel PDF. Vuoto = pagina di contatto delle impostazioni. L’elemento di contenuto può sostituirla con una propria pagina di inoltro. Senza una pagina (qui o nelle impostazioni) il pulsante viene omesso – il modulo non imposta mai un link da solo.'];
$l['privacy_page'] = ['Pagina della privacy', 'Link nel passaggio di contatto del modulo, nell’e-mail e nel PDF. Vuoto = pagina della privacy delle impostazioni. Senza una pagina non viene mostrato alcun link – La preghiamo di impostarla.'];
$l['newsletter_legend'] = 'Newsletter di questa lingua';
$l['nl_list_id'] = ['Lista Brevo', 'Lista per le iscrizioni dal check in questa lingua. Vuoto = lista del file di configurazione (newsletter.brevo_list_id).'];
$l['nl_template_id'] = ['Modello doppio opt-in', 'E-mail di conferma in questa lingua. Vuoto = modello del file di configurazione (newsletter.brevo_template_id).'];
$l['nl_redirect_page'] = ['Pagina di conferma', 'Pagina mostrata dopo il clic sul link di conferma. Vuoto = newsletter.brevo_redirect_url. Senza pagina di conferma non c’è iscrizione alla newsletter.'];
$l['nl_list_id_contao'] = ['Canale newsletter (Contao)', 'Canale newsletter di Contao per le iscrizioni dal check in questa lingua.'];
$l['nl_redirect_page_contao'] = ['Pagina di conferma', 'Pagina con il modulo Contao «Iscrizione»: il link di conferma nell’e-mail porta lì e attiva l’iscrizione. Il testo dell’e-mail proviene da quel modulo. Senza pagina di conferma non c’è iscrizione alla newsletter.'];
$l['ask_contact'] = ['Chiedere il permesso di contatto', 'Mostra nel modulo la casella facoltativa «Potete contattarmi una volta via e-mail riguardo a questo risultato». Testo e ora vengono salvati con il check.'];
$l['fe_contact'] = ['Testo della casella «contattare una volta»', ''];
$l['fe_gate_text_free'] = ['Modulo di contatto: testo senza licenza', 'Mostrato al posto di «Modulo di contatto: testo» finché non c’è una licenza; non promette alcuna e-mail. Predefinito: «Indichi il Suo nome e il Suo indirizzo e-mail …»'];
$l['fe_gate_legal_free'] = ['Modulo di contatto: nota sulla privacy senza licenza', 'Mostrata al posto di «Modulo di contatto: nota sulla privacy» finché non c’è una licenza. Il link alla pagina della privacy viene aggiunto automaticamente.'];
$l['fe_disclaimer_free'] = ['Risultato: nota senza licenza', 'Mostrata al posto di «Risultato: nota» finché non c’è una licenza; non menziona alcun report PDF.'];
$l['mail_note'] = 'Senza licenza questa e-mail con il report PDF arriva solo a Lei come gestore del sito (all’indirizzo indicato in «Copia a», altrimenti all’indirizzo del mittente), mai al visitatore.';
$l['nl_provider'] = ['Sistema di newsletter', 'Dove vanno le iscrizioni dal check in questa lingua: il modulo newsletter di Brevo o il modulo newsletter di Contao (doppio opt-in con l’e-mail di conferma di Contao). Dopo il salvataggio compaiono i campi corrispondenti.'];
$l['nl_provider_auto'] = 'Automatico (Brevo se installato)';
$l['nl_provider_options'] = ['brevo' => 'Brevo (modulo newsletter)', 'contao' => 'Newsletter di Contao'];
