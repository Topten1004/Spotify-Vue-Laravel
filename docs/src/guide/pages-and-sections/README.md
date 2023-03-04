# Pages & Sections

## How it works

Pages are the main components of any website and Muzzie is not an exception. However, Muzzie offers a dynamic way of creating pages. You can create as many custom pages as you want.

See [How to create a custom page](/guide/pages-and-sections/how-to-create-custom-page.md)

### Page Caracteristics


<img src="/assets/img/create_page.png">

Every page is characterized by a :

<strong>1.</strong> Name: the name of the page which is used to identify the page.

<strong>2.</strong> Icon: the icon that you see on the header of a page. 

<strong>3.</strong> Title: the title that you see on the header of a page.

<strong>4.</strong> Description: The little text under title.

<strong>5.</strong> Path: this is the path of the page and it is required to link it with your app. Exmple: a page with the path : <code>/my-page </code> will be available on <code>your-domain.com/my-page</code>

### Page content

The content of a page is a set of what we call <strong>sections</strong>

<img src="/assets/img/section.png">

Section is a swipeable horizontal container characterized by:

<strong>1.</strong> Title: the title of the section.

<strong>2.</strong> items : they can have one of the following types:

<ul-comp :items="['song','album','playlist','podcast']" />

The content of a section could be dynamically or automatically filled.

After you create your page and fill it with a section, you can optionally link it to a navigation item. See [How to link a page to a nav item](/guide/navigation/how-to-link-page-to-nav-item.md)
