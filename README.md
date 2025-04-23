# GameNight 🎉

**GameNight** is a web-based drinking game platform you can install on your phone and play with friends — even offline.

---

## 🔥 Overview

This is a Progressive Web App (PWA) designed to run on a simple Apache web server (no login or backend required). GameNight allows users to:

- Input player names
- Pick or search for public/private games
- Play randomized challenges (like Never Have I Ever, Spill the Tea, or physical challenges)
- Create, edit, and share games using a built-in JSON editor

---

## 🧩 Features

- 🕹️ Static game definitions via JSON files
- 🔄 JSON Editor for creating/editing games
- 📲 PWA-ready: install and play offline
- 🔍 Search games by title or ID
- 🔒 Public/private games
- 📸 Game previews with image, title, description
- 💬 Multi-language support (mainly Norwegian to start)
- 🧠 Modular: Easily add new challenge/game types
- 💸 Optional ads (AdSense/AdMob support planned)
- 📺 Future: Chromecast support for TV play
- 🛍️ Future: Links to merch store & drink recipes

---

## 📁 Project Structure

- `src/` - Core app logic and pages
- `games/` - JSON files representing different games
- `json-editor/` - A tool to create/edit game files
- `public/` - Icons, static files
- `service-worker.js` - Caches app for offline usage
- `manifest.json` - Required for PWA install

---

## ✍️ Game Format (JSON)

Each game file contains an array of prompts, like:

```json
{
  "id": "neverhave1",
  "title": "Never Have I Ever - Classic",
  "description": "The original party game, now on your phone.",
  "public": true,
  "type": "neverhaveiever",
  "language": "no",
  "prompts": [
    "Never have I ever kissed someone in this room.",
    "Never have I ever stolen something."
  ]
}
