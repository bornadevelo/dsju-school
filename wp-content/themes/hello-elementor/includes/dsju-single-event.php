<?php

$title = get_the_title();
$startDates = get_post_meta($eventId, "_EventStartDateUTC");
$endDates = get_post_meta($eventId, "_EventEndDateUTC");

$dateTimes = [];
for ($i = 0; $i < count($startDates); $i++){
    $startDateFormatted = date('d.m.Y. H:i', strtotime($startDates[$i]));
    $endDateTime = date('H:i', strtotime($endDates[$i]));

    $dateTimes[] = $startDateFormatted . '-' .$endDateTime . 'h';
}

$eventVenueNames = get_post_meta(get_the_ID(), "_EventVenueName");
$eventVenueAddresses = get_post_meta(get_the_ID(), "_EventVenueAddress");
$eventVenueCities = get_post_meta(get_the_ID(), "_EventVenueCity");

$locations = [];
for ($i = 0; $i < count($eventVenueNames); $i++){
    $location = $eventVenueNames[$i] . ', ' . $eventVenueAddresses[$i] . ', ' . $eventVenueCities[$i];
    $locations[] = $location;
}
$locations = array_unique($locations);

if($eventVenueNames[0] === 'online'){
    $locations = ['online'];
}

$trainers = get_post_meta($eventId, "_EventTrainers");
$trainersFormatted = '';
foreach ($trainers as $trainer){
    $trainersFormatted .= $trainer . ', ';
}
$trainersFormatted = rtrim($trainersFormatted, ', ');

$organizerFullName = get_post_meta($eventId, "_EventOrganizerFullName", true);
$organizerEmail = get_post_meta($eventId, "_OrganizerEmail", true);
$contact = $organizerFullName;
$contact .= $organizerEmail !== '' ? ', ' . $organizerEmail : '';

$goalsAndPurpose = get_post_meta($eventId, "_EventGoalsAndPurpose", true);
$content = get_the_content();
$learningOutcomes = get_post_meta($eventId, "_EventLearningOutcome");
$targetGroups = get_post_meta($eventId, "_EventTargetedGroups");
$institutions = get_post_meta($eventId, "_EventInstitution");
?>

<section class="ds-single-event">
    <div class="ds-single-event__breadcrumbs">
        <div class="ds-breadcrumb">
            <a href="<?php echo get_site_url(); ?>" class="ds-breadcrumb__item">Početna stranica</a>
            &raquo;
            <a href="<?php echo get_site_url(); ?>/programi-i-usluge" class="ds-breadcrumb__item">Programi i usluge</a>
            &raquo;
            <a href="<?php echo get_site_url(); ?>/programi-i-usluge/raspored" class="ds-breadcrumb__item">Raspored</a>
            &raquo;
            <a href="<?php echo get_permalink(); ?>" class="ds-breadcrumb__item"><?php echo get_the_title(); ?></a>
        </div>
    </div>
    <div class="ds-single-event__featured">
        <div class="ds-single-event__featured-inner">
            <h1 class="ds-single-event__title"><?php echo $title; ?></h1>
            <div class="ds-single-event__details">
                <div class="ds-single-event__detail">
                    <h2 class="ds-single-event__details-title">Datum i vrijeme održavanja</h2>
                    <?php foreach ($dateTimes as $dateTime){ ?>
                        <p class="ds-single-event__detail-text"><?php echo $dateTime; ?></p>
                    <?php } ?>
                </div>
                <div class="ds-single-event__detail">
                    <h2 class="ds-single-event__details-title">Mjesto održavanja</h2>
                    <?php foreach ($locations as $location){ ?>
                        <p class="ds-single-event__detail-text"><?php echo $location; ?></p>
                    <?php } ?>
                </div>
                <div class="ds-single-event__detail">
                    <h2 class="ds-single-event__details-title">Treneri</h2>
                    <p class="ds-single-event__detail-text"><?php echo $trainersFormatted; ?></p>
                </div>
                <div class="ds-single-event__detail">
                    <h2 class="ds-single-event__details-title">Kontakt</h2>
                    <p class="ds-single-event__detail-text"><?php echo $contact; ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="ds-single-event__content">
        <?php if ($goalsAndPurpose !== '') { ?>
            <div class="ds-single-event__content-item">
                <h2 class="ds-single-event__content-title">Ciljevi i svrha</h2>
                <p class="ds-single-event__content-paragraph"><?php echo $goalsAndPurpose; ?></p>
            </div>
        <?php } ?>
        <?php if ($content !== '') { ?>
            <div class="ds-single-event__content-item">
                <h2 class="ds-single-event__content-title">Sadržaj</h2>
                <p class="ds-single-event__content-paragraph"><?php echo $content; ?></p>
            </div>
        <?php } ?>
        <?php if (count($learningOutcomes) > 0) { ?>
            <div class="ds-single-event__content-item">
                <h2 class="ds-single-event__content-title">Ishodi učenja</h2>
                <ul class="ds-single-event__content-list">
                    <?php foreach ($learningOutcomes as $learningOutcome){ ?>
                        <li><?php echo $learningOutcome; ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        <?php if (count($targetGroups) > 0) { ?>
            <div class="ds-single-event__content-item">
                <h2 class="ds-single-event__content-title">Ciljana skupina</h2>
                <ul class="ds-single-event__content-list">
                    <?php foreach ($targetGroups as $targetGroup){ ?>
                        <li><?php echo $targetGroup; ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        <?php if (count($institutions) > 0) { ?>
            <div class="ds-single-event__content-item">
                <h2 class="ds-single-event__content-title">Institucije</h2>
                <ul class="ds-single-event__content-list">
                    <?php foreach ($institutions as $institution){ ?>
                        <li><?php echo $institution; ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
    </div>
</section>
