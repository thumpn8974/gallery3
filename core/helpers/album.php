<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2008 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * This is the API for handling albums.
 *
 * Note: by design, this class does not do any permission checking.
 */
class album_Core {
  /**
   * Create a new album.
   * @param integer $parent_id id of parent album
   * @param string  $name the name of this new album (it will become the directory name on disk)
   * @param integer $title the title of the new album
   * @param string  $description (optional) the longer description of this album
   * @return Item_Model
   */
  static function create($parent, $name, $title, $description=null, $owner_id=null) {
    if (!$parent->loaded || $parent->type != "album") {
      throw new Exception("@todo INVALID_PARENT");
    }

    $album = ORM::factory("item");
    $album->type = "album";
    $album->title = $title;
    $album->description = $description;
    $album->name = $name;
    $album->owner_id = $owner_id;
    $album->thumb_dirty = 1;
    $album->resize_dirty = 1;

    while (ORM::factory("item")
           ->where("parent_id", $parent->id)
           ->where("name", $album->name)
           ->find()->id) {
      $album->name = "{$name}-" . rand();
    }

    $album = $album->add_to_parent($parent);
    mkdir($album->file_path());
    mkdir(dirname($album->thumb_path()));
    mkdir(dirname($album->resize_path()));

    module::event("album_created", $album);

    return $album;
  }

  static function get_add_form($parent) {
    $form = new Forge("albums/{$parent->id}", "", "post", array("id" => "gAddAlbumForm"));
    $group = $form->group("add_album")->label(sprintf(_("Add Album to %s"), $parent->title));
    $group->input("name")->label(_("Name"));
    $group->input("title")->label(_("Title"));
    $group->textarea("description")->label(_("Description"));
    $group->hidden("type")->value("album");
    $group->submit(_("Create"));
    $form->add_rules_from(ORM::factory("item"));
    return $form;
  }

  static function get_edit_form($parent) {
    $form = new Forge("albums/{$parent->id}", "", "post", array("id" => "gEditAlbumForm"));
    $form->hidden("_method")->value("put");
    $group = $form->group("edit_album")->label(_("Edit Album"));
    if ($parent->id != 1) {
      $group->input("name")->label(_("Name"))->value($parent->name);
    }
    $group->input("title")->label(_("Title"))->value($parent->title);
    $group->textarea("description")->label(_("Description"))->value($parent->description);
    $group->hidden("type")->value("album");
    $group->submit(_("Modify"));
    $form->add_rules_from(ORM::factory("item"));
    return $form;
  }
}
