# Plugin / Theme Registry Rules

`e_PLUGIN/pluginpack.xml` (and `e_THEME/themepack.xml`) is the curated
registry that powers the **Find Plugins** / **Find Themes** screens in the
Plugin Manager. Each `<plugin>` entry points at a public GitHub repository
that contributes a single addon. These rules describe what an entry — and
the repo it points at — must look like for the entry to be installable.

## Validation rules

Every entry in `pluginpack.xml` must satisfy:

1. **`plugin.xml` is at the expected path.**
   The Plugin Manager fetches:

   ```
   https://raw.githubusercontent.com/{organization}/{repo}/refs/heads/{branch}/e107_plugins/{folder}/plugin.xml
   ```

   The repository must keep `plugin.xml` at `e107_plugins/{folder}/plugin.xml`,
   not at the repo root.

2. **`plugin.xml` declares required root attributes.**
   The `<plugin>` root element must define both `name` and `version`. A
   missing or empty attribute will block install.

3. **`<author>` element is present.**
   `plugin.xml` should declare `<author name="…" url="…"/>` so the
   marketplace card can attribute the work.

4. **`folder` in `pluginpack.xml` matches the actual directory in the
   repo.** A mismatch produces the same 404 as rule 1.

5. **`branch` exists on the remote repo.** Defaults to `main`.

6. **Repo is public** and the download `.zip` is reachable via
   `https://github.com/{org}/{repo}/archive/refs/heads/{branch}.zip`.

7. **Plugin installs cleanly** on a stock e107 install, with no conflicts
   against core tables, prefs, or other registry entries.

8. **Open-source licence** compatible with e107's GPL.

Rules 1–2 are checked at runtime (see *What happens if rules are not met*
below). Rules 3–8 remain manual review checks during the `pluginpack.xml`
PR.

## Minimal valid `<plugin>` entry (in `pluginpack.xml`)

```xml
<plugin
    folder="myplugin"
    organization="e107-Plugins"
    repo="myplugin"
    branch="main"
    name="My Plugin"
    compatibility="2.3">
    <description>One-line summary shown on the Find Plugins card.</description>
</plugin>
```

Required attributes: `folder`, `organization`, `repo`. Optional but
strongly recommended: `branch` (defaults to `main`), `name`,
`compatibility`, and a `<description>` child element.

## Minimal valid `plugin.xml` (in the contributor's repo, at
`e107_plugins/{folder}/plugin.xml`)

```xml
<?xml version="1.0" encoding="utf-8"?>
<plugin name="My Plugin" version="1.0.0" date="2026-01-15" compatibility="2.3">
    <author name="Jane Doe" url="https://example.com/jane"/>
    <description>Full description shown on the Find Plugins card.</description>
    <category>tools</category>
</plugin>
```

The `name` and `version` root attributes are mandatory — the runtime guard
will refuse to enable the Install button without them.

## What happens if rules are not met

When the Plugin Manager renders the Find Plugins screen, it fetches each
entry's remote `plugin.xml`. If the fetch fails (rule 1, 4, or 5) or the
payload is missing required fields (rule 2), the card still renders using
whatever data `pluginpack.xml` supplies, but the **Install button is
disabled** and its tooltip explains why:

* `plugin.xml not found at expected path (e107_plugins/{folder}/plugin.xml)`
  — the fetch returned 404 or unparseable XML.
* `plugin.xml is missing required fields: name, version` — at least one of
  the required `<plugin>` root attributes is empty or absent.

Other users see the entry exists but cannot trigger an install that would
fail mid-flight. To re-enable a card, fix the repo (move `plugin.xml`
under `e107_plugins/{folder}/`, add the missing attributes) or correct
the registry entry, then resubmit a `pluginpack.xml` PR.
