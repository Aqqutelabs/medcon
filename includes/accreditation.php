<?php

function accreditation_documents(): array {
    return [
        'cac' => [
            'stage' => '01', 'label' => 'Corporate Registration', 'status' => 'Registered',
            'title' => 'Registered with the Corporate Affairs Commission', 'issuer' => 'Corporate Affairs Commission, Nigeria',
            'file' => 'assets/documents/CERTIFICATE - MEDCON EDUCATIONAL SERVICE AND CONSULTANCY LTD.pdf',
            'preview' => 'img/medcon-cac.jpg',
            'facts' => ['Company' => 'Medcon Educational Service and Consultancy Ltd', 'Registration number' => '9626491', 'Incorporation date' => '22 June 2026', 'Company type' => 'Private company limited by shares'],
            'explanation' => 'This registration confirms that Medcon Edu operates through a legally incorporated Nigerian company.',
        ],
        'pltci' => [
            'stage' => '02', 'label' => 'Institutional Authorization', 'status' => 'Authorized',
            'title' => 'Authorized to Support PLTCI Applicants in Nigeria', 'issuer' => 'PLTCI College of Medicine',
            'file' => 'assets/documents/015.pdf', 'preview' => 'img/PLTCI.jpg', 'facts' => [],
            'wdoms_url' => 'https://search.wdoms.org/home/SchoolDetail/F0006833',
            'wdoms_label' => 'View PLTCI College of Medicine in WDOMS',
            'scope' => ['Programme information', 'Student and parent counselling', 'Application-document support', 'Admission communication coordination', 'Pre-departure support', 'Administrative visa-document guidance'],
            'explanation' => 'This authorization enables Medcon Edu, within the stated scope and collaboration arrangement, to support Nigerian students applying to PLTCI College of Medicine.',
        ],
        'tmtcs' => [
            'stage' => '03', 'label' => 'Institutional Recognition', 'status' => 'Recognized',
            'title' => 'Student Recruitment and Support Authorization', 'issuer' => 'The Manila Times College School of Medicine',
            'file' => 'assets/documents/certificate_printable.pdf.pdf', 'preview' => 'img/TMTC.jpg', 'facts' => [],
            'wdoms_url' => 'https://search.wdoms.org/home/SchoolDetail/F0007893',
            'wdoms_label' => 'View TMTCS College of Medicine in WDOMS',
            'explanation' => 'This document recognizes Medcon Edu’s role in supporting prospective Nigerian students under the authorization and collaboration structure stated by the issuing institution.',
        ],
    ];
}

function accreditation_document_available(array $document): bool {
    return is_file(dirname(__DIR__) . '/' . ($document['preview'] ?? $document['file']));
}

function render_certificate_preview(string $key, array $document, string $buttonLabel = 'View Official Document'): void {
    $available = accreditation_document_available($document);
    $pdfAvailable = is_file(dirname(__DIR__) . '/' . $document['file']);
    $viewerAsset = $pdfAvailable ? $document['file'] : $document['preview'];
    ?>
    <div class="certificate-preview<?= $available ? '' : ' is-unavailable' ?>">
        <div class="certificate-sheet"><span><?= $pdfAvailable ? 'PDF' : 'Certificate' ?></span><img src="<?= e(site_url($document['preview'])) ?>" alt="Preview of <?= e($document['title']) ?>" loading="lazy"></div>
        <div class="certificate-actions">
            <?php if ($available): ?>
                <button class="btn btn-outline" type="button" data-certificate-open data-document-url="<?= e(site_url($viewerAsset)) ?>" data-document-title="<?= e($document['title']) ?>"><?= e($buttonLabel) ?></button>
                <?php if ($pdfAvailable): ?><a class="document-download" href="<?= e(site_url($document['file'])) ?>" download>Download PDF</a><?php endif; ?>
            <?php else: ?>
                <span class="document-unavailable" role="status">Certificate preview unavailable</span>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function render_certificate_dialog(): void { ?>
    <dialog class="certificate-dialog" data-certificate-dialog aria-labelledby="certificate-dialog-title">
        <div class="certificate-dialog-head"><strong id="certificate-dialog-title" data-certificate-title>Official document</strong><button type="button" data-certificate-close aria-label="Close document viewer">Close</button></div>
        <iframe data-certificate-frame title="Official accreditation document"></iframe>
        <p>If the document does not display, <a href="#" data-certificate-fallback target="_blank" rel="noopener">open it in a new tab</a>.</p>
    </dialog>
<?php }
