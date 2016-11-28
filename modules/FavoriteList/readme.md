<!--
 _____                     _ _       _     _     _   
|  ___|_ ___   _____  _ __(_) |_ ___| |   (_)___| |_ 
| |_ / _` \ \ / / _ \| '__| | __/ _ \ |   | / __| __|
|  _| (_| |\ V / (_) | |  | | ||  __/ |___| \__ \ |_ 
|_|  \__,_| \_/ \___/|_|  |_|\__\___|_____|_|___/\__|
                                                     
-->

# FavoriteList
The FavoriteList component includes a simple collection (also called "wishlist") of items, which can be shared trough E-Mail or printed-out.

## Installation
To install this component, you have to import blueprints from the `View/Template/Blueprint` directory into Cloudrexx.

### Blueprint
> Software blueprints focus on one application aspect, for clarity of presentation and to ensure that all of the relevant logic is localized.
> [Wikipedia](https://en.wikipedia.org/wiki/Software_blueprint)

#### PDF
You have to import the following HTML PDF template in the "PDF Templates" (`/cadmin/Config/Pdf`) section.

Template:
- PdfCatalog.html

Choose/activate your previously added template (`/cadmin/FavoriteList/Settings` > PDF template > Template).

#### Mail
You have to create the following mail templates in "Mail Template" (`/cadmin/FavoriteList/Settings/Mailing`) section.

As an example, you can import the HTML layout from the predefined templates in the blueprint folder.

##### Mail

HTML layout: `MailMail.html`

| Option          | Value |
|-----------------|-------|
| Key             | mail  |
| Use HTML format | check |

The following option is overwritten/set on submit:
- Recipient email address (to:)

##### Recommendation

HTML layout: `MailRecommendation.html`

| Option          | Value          |
|-----------------|----------------|
| Key             | recommendation |
| Use HTML format | check          |

The following options are overwritten/set on submit:
- Sender name
- Sender email address (from:)
- Recipient email address (to:)

##### Inquiry

HTML layout: `MailInquiry.html`

| Option          | Value   |
|-----------------|---------|
| Key             | inquiry |
| Use HTML format | check   |

These defined options are all mandatory, this means you can customize all other (not listed) options freely.

## Documentation
There are 3 different methods to share your collection through E-Mail.

 1. Mail (E-Mail to the list owner)
 2. Inquiry (E-Mail with custom defined fields to the website admin)
 3. Recommendation (E-Mail to a specified person)

In each mail, the list is attached as a PDF.

As previous mentioned, of course you can print-out your list.

An ERD can be found here: `modules/FavoriteList/Doc/ModuleFavoriteListErd.mwb`

### Backend
#### Lists/Catalog
| Link                         |
|------------------------------|
| /cadmin/FavoriteList/Catalog |

Lists are the collections (container) of Favorites, they're mainly auto-generated.
If you want, you can add your own collection (click "Add" button).

They're auto-deleted if the session (user session who created the list) is going to be destroyed.
Thus we can ensure no unnecessary data overflow.

#### Favorites
| Link                          |
|-------------------------------|
| /cadmin/FavoriteList/Favorite |

Favorites are the entries for your collection (catalog).
Each entry has to be assigned to a catalog and a defined name.

#### Settings
##### General
| Link                          |
|-------------------------------|
| /cadmin/FavoriteList/Settings |

First, here ("Functions") you can choose which available functions you like to activate.
They are listed in the frontend (Default and Block).

Under "PDF template" there are all available options for the PDF generation defined.
Logo and Address is pasted in the header. The Footer obviously in the footer.
They are self explanatory.

##### Mail
| Link                                  |
|---------------------------------------|
| /cadmin/FavoriteList/Settings/Mailing |

For Developer: [Development Core MailTemplate](http://wiki.contrexx.com/en/index.php?title=Development_Core_MailTemplate)

This sections takes an important part of this component, there the Mail Templates are defined.
The Key has to be the same as the site command of the Content Manager.
Each option can be set (recommended) individually.

##### Inquiry form fields
| Link                                    |
|-----------------------------------------|
| /cadmin/FavoriteList/Settings/FormField |

Because the form of the inquiry section is customizable, the fields can be set and ordered individually.
There are 6 types available. The values of types like "Selection menu", "Selection field" or "Checkbox" are comma separated.

### Frontend
#### Default
| Command       |
|---------------|
| [not defined] |

All Favorites from your Catalog (current session) are listed.

Basicly, all placeholders are self explanatory.
All available options are listed in the predefined examples (`modules/FavoriteList/View/Template/Frontend/Default.html`).

#### Block
It's an element which can be placed everywhere.
But mainly used as a sidebar.

Basicly, all placeholders are self explanatory.
All available options are listed in the predefined examples (`themes/standard_4_0/favoritelist_block.html` & `themes/standard_4_0/favoritelist_block_list.html`).
For each theme you have to created the necessary two templates.

The `favoritelist_block_list.html` stores the placeholder for the AJAX-Requests, this means: the content is loaded dynamically on every change to your catalog.

#### JavaScript
To modify your catalog, there are 3 different actions you can perform.

##### Add Favorite
To add a Favourite dynamically through ajax without a tedious page reload.
Your custom data is set as data-attributes. The following attributes are available:

 - title (must be defined)
 - link
 - description
 - message
 - price
 - image1
 - image2
 - image3

In this example we use an onclick event on a link (most common).
Of course, the method can be called everywhere.

**Example:**
```
<a data-title="My Favorite Entry" data-description="Lorem ipsum dolor sit amet," href="#" onclick="cx.favoriteListAddFavorite(this);">Add me</a>
```

##### Remove Favorite
This removes an Favorite obviously.
The parameter is just the `id ` of your Favorite.

**Example:**
```
<a href="#" onclick="cx.favoriteListRemoveFavorite(1);">Delete me</a>
```

#### Print
| Command |
|---------|
| print   |

Basicly, all placeholders are self explanatory.
All available options are listed in the predefined examples (`modules/FavoriteList/View/Template/Frontend/Print.html`).

The custom PDF Template is set here: `/cadmin/Config/Pdf`

#### Mail
| Command |
|---------|
| mail    |

Basicly, all placeholders are self explanatory.
All available options are listed in the predefined examples (`modules/FavoriteList/View/Template/Frontend/Mail.html`).

#### Recommendation
| Command        |
|----------------|
| recommendation |

Basicly, all placeholders are self explanatory.
All available options are listed in the predefined examples (`modules/FavoriteList/View/Template/Frontend/Recommendation.html`).

#### Inquiry
| Command |
|---------|
| inquiry |

Basicly, all placeholders are self explanatory.
All available options are listed in the predefined examples (`modules/FavoriteList/View/Template/Frontend/Inquiry.html`).
