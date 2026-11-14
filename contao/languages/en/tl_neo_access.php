<?php

declare(strict_types=1);

// Sections of the website check on "Access keys and licences" (page of the Basic bundle)

$GLOBALS['TL_LANG']['tl_neo_access']['websitecheck_psi_legend_name'] = 'Google PageSpeed';
$GLOBALS['TL_LANG']['tl_neo_access']['websitecheck_psi_key'] = ['PageSpeed key', 'Google API key for PageSpeed Insights (measures load time and quality). Without it the form on the website accepts no checks. Stored encrypted and never shown again. Leaving it empty keeps the stored key.'];
$GLOBALS['TL_LANG']['tl_neo_access']['websitecheck_psi_missing'] = 'No PageSpeed key is stored yet. Until then the form on the website accepts no checks.';
$GLOBALS['TL_LANG']['tl_neo_access']['websitecheck_ai_missing'] = 'No AI key is stored yet. Until then the check runs without the AI review.';
