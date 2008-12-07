<? defined("SYSPATH") or die("No direct script access."); ?>
<div id="gAlbumHeader">
  <ul id="gItemMenu">
    <li><a href="#" title="<?= _("View album") ?>">
        <img src="<?= $theme->url("images/ico-view-album.png") ?>"
             alt="<?= _("View album") ?>" /></a></li>
    <li><a href="#" title="<?= _("View album in hybrid mode") ?>">
        <img src="<?= $theme->url("images/ico-view-hybrid.png") ?>"
             alt="<?= _("View album in hybrid mode") ?>" /></a></li>
    <li><?= $theme->album_top() ?></li>
    <li><a href="<?= url::site("/form/add/photos/$item->id") ?>" title="<?= _("Add an item") ?>"
        class="gButtonLink gDialogLink"><?= _("Add an item") ?></a></li>
  </ul>

  <h1><?= $item->title_edit ?></h1>
  <div class="gDescription"><?= $item->description_edit ?></div>
</div>

<ul id="gAlbumGrid">
  <? foreach ($children as $i => $child): ?>
  <? $album_class = ""; ?>
  <? if ($child->is_album()): ?>
  <? $album_class = "gAlbum "; ?>
  <? endif ?>
  <li class="gItem <?= $album_class ?>">
    <?= $theme->thumbnail_top($child) ?>
    <a href="<?= url::site("{$child->type}s/{$child->id}") ?>">
      <img id="gPhotoID-<?= $child->id ?>" class="gThumbnail"
           alt="photo" src="<?= $child->thumbnail_url() ?>"
           width="<?= $child->thumbnail_width ?>"
           height="<?= $child->thumbnail_height ?>" />
    </a>
    <h2><?= $child->title_edit ?></h2>
    <?= $theme->thumbnail_bottom($child) ?>
    <ul class="gMetadata">
      <?= $theme->thumbnail_info($child) ?>
    </ul>
  </li>
  <? endforeach ?>
</ul>
<?= $theme->album_bottom() ?>

<?= $theme->pager() ?>
