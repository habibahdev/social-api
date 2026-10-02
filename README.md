# API Réseau Social - Post/Comment/Like

API REST développée avec **Symfony 7** et **PHP 8.2**, testable ia **Postman**

---

## Sommaire

- [Pré-requis](#prérequis)
- [Installation](#installation)
- [Entités](#entités)
- [Routes de l'API](#routes-de-lapi)
    - [Posts](#posts)
    - [Comments](#comments)
    - [Likes](#likes)
- [Sérialisation](#sérialisation)
- [Exemples Postman](#exemples-postman)

---

## Prérequis

* PHP >= 8.2
* Symfony CLI
* MySQL

---

## Installation

```bash
git clone git@github.com:habibahdev/social-api.git
cd social-api
composer install
composer prepare
symfony serve -d
```

Les fixtures crééent 1 utilisateurs (mot de passe : `password`), chacun avec 5 posts, likés par 5 autres utilisateurs, et 10 commentaires par post.

---

## Entités

| Entité | Champs principaux |
|---|---|
| `User` | `id`, `email`, `password`, `name` |
| `Post` | `id`, `content`, `publishedAt`, `author`, `likedBy` |
| `Comment` | `id`, `message`, `publishedAt`, `author`, `post` |

---

## Routes de l'API
### Posts

| Méthode | URL | Description | JSON
|---|---|---|---|
| GET | `/api/posts` | Liste de tous les posts | - |
| GET | `/api/posts/{id}` | Détail d'un post | - |
| POST | `/api/posts` | Crée un post | `{ "content": "...", "authorId": 1 }` |
| PUT | `/api/posts/{id}` | Modifie le contenu d'un post | `{ "content": "..." }` |
| DELETE | `/api/posts/{id}` | Supprime un post (et ses commentaires) | - |

---

### Comments

| Méthode | URL | Description | JSON
|---|---|---|---|
| GET | `/api/posts/{postId}/comments` | Liste les commentaires d'un post | - |
| GET | `/api/posts/comments/{id}` | Détail d'un commentaire | - |
| POST | `/api/posts/{postId}/comments` | Ajoute un commentaire à un post | `{ "message": "...", "authorId": 1 }` |
| PUT | `/api/posts/comments/{id}` | Modifie un commentaire | `{ "message": "..." }` |
| DELETE | `/api/posts/comments/{id}` | Supprime un commentaire | - |

---

### Likes

| Méthode | URL | Description | JSON
|---|---|---|---|
| GET | `/api/posts/{postId}/likes` | Liste les utilisateurs ayant liké un post | - |
| POST | `/api/posts/{postId}/likes` | Like un post | `{ "userId": 1 }` |
| DELETE | `/api/posts/{postId}/likes/{userId}` | Retire un like | - |

---

## Sérialisation

Les réponses utilisent le groupe de sérialisation `get`.

Exemple de réponse `GET /api/posts/1` :

```json
{
    "id": 1,
    "content": "content 1",
    "publishedAt": "2026-09-14T10:00:00+00:00",
    "author": {
        "id": 1,
        "email": "email1@api.com",
        "name": "name1"
    },
    "likedBy": [
        { "id": 2, "email": "email2@api.com", "name": "name2" }
    ]
}
```

---

## Exemples Postman

**Créer un post**
```
POST http://localhost:8000/posts
Content-Type: application/json

{
    "content": "Premier post",
    "authorId": 1
}
```

**Commenter un post**
```
POST http://localhost:8000/posts/1/comments
Content-Type: application/json

{
    "message": "Commentaire post",
    "authorId": 1
}
```

**Liker un post**
```
POST http://localhost:8000/posts
Content-Type: application/json

{
    "userId": 1
}
```

**Retirer un like**
```
DELETE http://localhost:8000/posts/1/likes/3
```