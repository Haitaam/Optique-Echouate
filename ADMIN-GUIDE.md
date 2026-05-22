# Guide d'utilisation — Administration des Lunettes

> **Optique Échouate — Gestion du Catalogue**

---

## 1. Accès au panneau d'administration

**URL :** `http://127.0.0.1:8000/admin/glasses`

Le panneau est accessible sans authentification pour le moment. Il permet de gérer l'intégralité du catalogue de lunettes.

---

## 2. Présentation de la liste des produits

La page principale affiche un tableau avec tous les produits en base :

| Colonne | Description |
|---|---|
| Image | Aperçu de la photo (ou icône rouge si manquante) |
| Nom | Nom du produit |
| Marque | Marque détectée ou modifiée |
| Genre | Homme / Femme / Unisexe |
| Forme | Forme de la monture |
| Couleur | Pastille + nom de la couleur |
| Prix | Prix en MAD (Moroccan Dirham) |

**Boutons en haut :**
- **Synchroniser le Catalogue** → Reconstruit toute la base depuis les fichiers
- **Ajouter** → Page d'import de nouvelles images

**Indicateur orange :** si des images sont manquantes, un avertissement s'affiche.

---

## 3. Ajouter de nouvelles lunettes

### 3.1. Préparation des fichiers images

Règles importantes pour les noms de fichiers :

```
[marque]-[modele]-[couleur].jpg
```

**Exemples qui fonctionnent :**
| Fichier | Marque détectée | Nom généré |
|---|---|---|
| `ray-ban-aviator-001.jpg` | Ray-Ban | Ray-Ban Aviator 001 |
| `versace-greca-black.jpg` | Versace | Versace Greca Black |
| `biaggi-bg9085-c4.jpg` | Biaggi | Biaggi Bg9085 |
| `tom-ford-falconer-tf884.jpg` | Tom Ford | Tom Ford Falconer Tf884 |

**Dossier de destination :** les images doivent être placées dans `public/images/glasses/` dans l'un de ces sous-dossiers :

| Dossier | Catégorie | Usage |
|---|---|---|
| `men/` | Optique Homme | Lunettes de vue homme |
| `women/` | Optique Femme | Lunettes de vue femme |
| `sunglasses/men/` | Solaire Homme | Lunettes soleil homme |
| `sunglasses/women/` | Solaire Femme | Lunettes soleil femme |
| `luxury/` | Luxe Unisexe | Pièces de collection luxe |

### 3.2. Import via l'interface

1. Cliquez sur **Ajouter** en haut de la liste
2. Glissez vos images dans la zone ou cliquez pour sélectionner
3. Choisissez la **Catégorie** (détermine le dossier de destination)
4. Choisissez le **Genre**
5. Cliquez sur **Importer**

Le système détecte automatiquement :
- La **marque** à partir du nom du fichier
- La **forme** de la monture (round, square, aviator, etc.)
- La **couleur** par analyse de l'image
- Les **tags** extraits du nom

Vous pourrez modifier ces valeurs après import.

### 3.3. Import via le dossier (pour les lots)

Placez directement les images dans les dossiers ci-dessus, puis cliquez sur **Synchroniser le Catalogue** ou exécutez :

```bash
php artisan glasses:sync
```

Toutes les nouvelles images seront automatiquement ajoutées.

---

## 4. Modifier un produit existant

1. Cliquez sur **Modifier** dans la liste
2. Modifiez les champs souhaités :

| Champ | Description |
|---|---|
| **Nom** | Nom complet du produit |
| **Marque** | Marque (sélection dans la liste) |
| **Forme** | Forme de la monture |
| **Genre** | Homme / Femme / Unisexe |
| **Matière** | Acetate, Titanium, etc. |
| **Couleur** | Couleur dominante |
| **Prix (MAD)** | Prix ÷ 10 (ex: 1500 MAD → 150) |
| **En vedette** | Cochez pour afficher en page d'accueil |
| **Luxe** | Cochez pour marquer comme pièce luxe |
| **Catégories** | Optique, Solaire, etc. |
| **Tags** | Mots-clés séparés par des virgules |

3. Pour **remplacer l'image**, cliquez sur « Remplacer l'image » en bas de l'aperçu
4. Cliquez sur **Enregistrer**

> La marque détectée automatiquement peut être modifiée ici si elle est incorrecte.

---

## 5. Supprimer un produit

1. Dans la liste, cliquez sur **Supprimer**
2. Une confirmation vous est demandée
3. La case **fichier** est cochée par défaut :
   - **Cochiez** → supprime le produit + le fichier image du dossier
   - **Décochiez** → supprime seulement la ligne dans la base, l'image reste sur le disque

**Ce qui se passe après suppression :**
- Le produit disparaît immédiatement du site
- Le cache est vidé automatiquement
- Si la case fichier est cochée, l'image est définitivement supprimée

---

## 6. Synchronisation complète du catalogue

### Quand l'utiliser ?
- Après avoir ajouté/supprimé des fichiers dans les dossiers manuellement
- Si des images sont manquantes ou que des produits ne s'affichent pas correctement
- Pour reconstruire entièrement la base depuis le disque

### Comment faire ?

**Via l'interface :**
1. Cliquez sur **Synchroniser le Catalogue**
2. Patientez quelques secondes
3. Un message confirme le succès

**Via la ligne de commande :**
```bash
php artisan glasses:sync
```

**Prévisualisation sans risque :**
```bash
php artisan glasses:sync --dry-run
```
Cette commande montre ce qui va être importé sans modifier la base.

### Ce que fait la synchronisation :

1. Vide tous les produits de la base de données
2. Scanne tous les fichiers dans `public/images/glasses/`
3. Analyse chaque image (couleur, marque, forme)
4. Crée un produit pour chaque fichier trouvé
5. Assigne les catégories (optique / solaire)
6. Vide les caches

### Résultat attendu :
- **Tous** les fichiers image ont un produit correspondant
- **Aucun** produit orphelin (sans image)
- Les filtres (marque, genre, forme) sont automatiquement à jour

---

## 7. Gestion des filtres côté client

Les filtres sur la page **Produits** sont automatiquement mis à jour :

- **Marque** → liste des marques présentes en base
- **Genre** → Homme / Femme / Unisexe
- **Forme** → Round, Square, Aviator, etc.
- **Couleur** → toutes les couleurs détectées
- **Tags** → mots-clés extraits des noms

Aucune manipulation manuelle n'est nécessaire. Après chaque synchronisation, les filtres reflètent exactement les données en base.

---

## 8. Erreurs fréquentes et solutions

| Problème | Cause | Solution |
|---|---|---|
| Une image ne s'affiche pas | Fichier supprimé du dossier | Lancer une synchronisation |
| Produit en double | Même image dans 2 dossiers | Supprimer le doublon + resynchroniser |
| Marque incorrecte | Nom de fichier non standard | Modifier manuellement dans l'admin |
| La couleur détectée est fausse | L'analyse de l'image a échoué | Changer la couleur manuellement |
| Un produit apparaît sans image | Le fichier a été renommé | Supprimer le produit ou synchroniser |

---

## 9. Architecture technique (pour information)

```
public/images/glasses/    ← Source de vérité (dossier des images)
         ├── men/         ← Lunettes vue homme
         ├── women/       ← Lunettes vue femme
         ├── luxury/      ← Collection luxe
         └── sunglasses/
              ├── men/    ← Lunettes soleil homme
              └── women/  ← Lunettes soleil femme

Base de données           ← Miroir du dossier (regénéré par sync)
         └── products     ← 1 produit = 1 image

Commande de sync :        php artisan glasses:sync
Page admin :              /admin/glasses
```

**Règle d'or :** Le dossier `public/images/glasses/` est la source unique de vérité. La base de données est un reflet qui doit être synchronisé régulièrement.
