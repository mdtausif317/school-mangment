# al-menu-add — Menu Management Page

Admin page for creating sidebar menu entries, viewing the full menu hierarchy, toggling visibility, and managing per-page action buttons.

---

## Overview

| Item | Detail |
|------|--------|
| **File** | `aameral-dev/pages/al-menu-add.php` |
| **URL** | `{BASE_URL}/index.php?page=al-menu-add` |
| **Access** | Logged-in users with page permission for slug `al-menu-add` |
| **Related page** | `al-menu.php` — user access assignment for menus and buttons |

The page uses a two-column layout:

- **Left** — form to add a new parent or child menu item
- **Right** — live accordion tree of existing menus, with show/hide toggles and button management

---

## Dependencies

### PHP includes

| File | Role |
|------|------|
| `al-header.php` | Layout, session, shared assets |
| `al-footer.php` | Footer scripts |
| `classes/AccessMenu.php` | Menu CRUD and tree data |

### JavaScript

| Asset | Role |
|-------|------|
| `assets/js/al-main.js` | `al_post_form()`, `al_loading_div()`, `notify_it()` |
| jQuery | AJAX and DOM events |
| Bootstrap 5 | Accordion, modal, form controls |

### Backend API

All write operations POST to `{ajax_url}/api`, routed through `ajax/post.php` → `ajax/modules/al-access-menu.php`.

---

## Page load (server-side)

On render, PHP calls:

```php
$as = new AccessMenu();
$parents = $as->getParentMenus();           // Parent dropdown options
$allMenus = $accessMenu->getAllMenusWithDisplay();  // Right-panel tree
```

The tree is built recursively via `buildMenuTreeWithDisplay()` and rendered as a Bootstrap accordion. Child items and leaf parents can load buttons with `AccessMenu::getMenuButtons($menu_id)`.

---

## Features

### 1. Add new menu item

**Form ID:** `create_new_page`

| Field | Name | Required | Description |
|-------|------|----------|-------------|
| Parent Menu | `parent_id` | No | Empty = top-level parent menu |
| Menu Title | `title` | Yes | Label shown in the sidebar |
| Slug | `slug` | Yes | Route key: `?page={slug}` (e.g. `project-add`) |
| Icon | `icon` | No | FontAwesome class (default: `fas fa-circle`) |
| Display in Menu | `display_in_menu` | No | `0` = Yes (visible), `1` = No (hidden) |

**Slug auto-generation:** Typing in **Menu Title** auto-fills the slug (lowercase, hyphenated). Manual edits are preserved until **Regenerate** is clicked.

**Submit:** Calls `al_post_form('create_new_page')`, which POSTs the form to the API.

**On success:** `AccessMenu::addMenu()` inserts into `al_pages_menu_list`, assigns `sort_order`, and grants page access to the logged-in user via `addUserPageAccess()`.

---

### 2. Menu structure viewer

The right panel shows the full hierarchy:

- **Parents with children** — expandable accordion rows with child count badge
- **Leaf menus** — direct link to `?page={slug}` (opens in new tab)
- **Hidden items** — `opacity-50` styling and a **Hidden** badge when `display = 1`

---

### 3. Show / hide toggle

Each menu row has a switch (`.menu-display-toggle`).

| Checkbox | `display` sent | Effect |
|----------|----------------|--------|
| Checked | `0` | Visible in sidebar |
| Unchecked | `1` | Hidden from sidebar |

**API:** `update_menu_display=1`, `menu_id`, `display`  
**Handler:** `AccessMenu::updateMenuDisplay()`

The UI updates in place on success (opacity, badge, label text) without a full reload.

---

### 4. Page buttons

Buttons are sub-actions tied to a menu (e.g. Add, Edit, Delete) used for fine-grained permissions.

**Add button** — opens `#addButtonModal`:

| Field | Name | Required |
|-------|------|----------|
| Menu ID | `menu_id` (hidden) | Yes |
| Button Title | `button_title` | Yes |
| Button Link | `button_link` | Yes (slug-style identifier) |
| Status | `button_status` | No (`0` = Active, `1` = Inactive) |

**API:** `add_button=1` + form fields  
**Handler:** `AccessMenu::addButton()` — also grants button access to the creator.

**Delete button** — confirmation dialog, then:

**API:** `delete_button=1`, `button_id`  
**Handler:** `AccessMenu::deleteButton()` — removes auth rows and the button record.

After add/delete, the page reloads after ~1 second.

---

## API reference

Endpoint: `POST {ajax_url}/api`

### Create menu

```
create_new_page=1
parent_id=          (optional)
title=              (required)
slug=               (required)
icon=               (optional)
display_in_menu=0|1
```

### Update visibility

```
update_menu_display=1
menu_id={int}
display=0|1         (0=show, 1=hide)
```

### Add button

```
add_button=1
menu_id={int}
button_title={string}
button_link={string}
button_status=0|1
```

### Delete button

```
delete_button=1
button_id={int}
```

All handlers return JSON: `{ "status": "success"|"error", "message": "..." }`

---

## Database tables

| Table | Purpose |
|-------|---------|
| `al_pages_menu_list` | Menu items (`parent_id`, `slug`, `title`, `icon`, `sort_order`, `display`) |
| `al_pages_auth` | User/designation access to menu pages |
| `al_pages_buttons` | Action buttons per menu |
| `al_pages_buttons_auth` | User access to buttons |

### `display` column semantics

- `0` — item appears in the sidebar (`getSidebarMenu()` uses `WHERE display = 0`)
- `1` — item is hidden from the sidebar but may still exist for routing/permissions

---

## UI / layout notes

- Default navbar/search bar is hidden on this page for a focused admin layout
- Sticky top bar with back button (`history.back()`)
- Brand color: `#0a5f47`
- Responsive breakpoints at 992px, 768px, 640px, and 480px
- Child menus and buttons use progressive indentation in the tree

---

## File map

```
aameral-dev/
├── pages/
│   └── al-menu-add.php          ← This page (view + inline JS/CSS)
├── classes/
│   └── AccessMenu.php           ← addMenu, getMenuButtons, toggles, button CRUD
├── ajax/
│   ├── post.php                 ← API router
│   └── modules/
│       └── al-access-menu.php   ← POST handlers for this page
└── assets/js/
    └── al-main.js               ← al_post_form() helper
```

---

## Related files

| File | Notes |
|------|-------|
| `pages/menu-add.php` | Older standalone menu-add UI (no `al-header` integration) |
| `pages/al-menu.php` | Assign menu/button permissions to users |
| `backup-07-02-2026/pages/al-menu-add.php` | Snapshot copy from Feb 2026 backup |

---

## Common workflows

### Add a top-level menu

1. Open `?page=al-menu-add`
2. Leave **Parent Menu** as “This is a Parent Menu”
3. Enter title, confirm slug, set icon
4. Click **Add Menu**

### Add a child under an existing parent

1. Select the parent in **Parent Menu**
2. Fill title, slug, and icon
3. Submit — child appears under that parent in the tree after refresh

### Hide a menu without deleting it

1. Find the item in **Menu Structure**
2. Turn off the **Show** switch — item gets **Hidden** badge and is excluded from the sidebar

### Add action buttons for permissions

1. Click **Add Button** on a leaf menu (or child item)
2. Enter title and link slug
3. Assign button access to users on `al-menu.php`

---

## Troubleshooting

| Issue | Likely cause |
|-------|----------------|
| “Form submission function not available” | `al-main.js` not loaded via `al-header.php` |
| “A menu with this slug already exists” | Duplicate `slug` in `al_pages_menu_list` |
| Page not found at `?page=al-menu-add` | Slug not registered in menu DB, or file missing from `pages/` |
| Access denied / redirect to home | User lacks `al_pages_auth` entry for this slug |
| AJAX errors | Check `ajax_url` config and `ajax/modules/al-access-menu.php` is included by the router |

---

## Security notes

- Page access is enforced in `index.php` via `checkUserAuth()` before the file is included
- New menus and buttons automatically grant access to the creating user
- Slug and title values are escaped before database insert (`addslashes`)
- Admin-only operations should remain behind menu/auth checks — do not expose this page without login
