## GrabAssets

GrabAssets is a PicoCMS plugin designed to do one thing and one thing only: get you a list of all your assets in a nice big array.

### Installation

Installation is easy. Clone GrabAssets into your PicoCMS folder. It consists of a single PHP file named, appropriately enough, `GrabAssets.php`.

Next, add the two lines to your config.yml file:

    assets_dir: assets                              # path to assets
    supported_assets: [gif, jpg, jpeg, png, svg]    # supported assets

That's it!

### What it does

When you load a page, GrabAssets iterates through your assets folder and creates an array named `array_of_assets` that is accessible via Twig. For example, if you want a page full of images, you could do something like the followiing in Twig:

    {% for asset in array_of_assets %}
        <pre> {{ asset }} </pre>
    {% endfor %}

This would display the asset's location. Not terribly useful! However, suppose you did the following?

    {% for asset in array_of_assets %}
        <img src="{{ base_url }}/{{ asset }}">
    {% endfor %}
    
It would then display all the images in your asset directory and sub-directories.

### Let's Get Precise

Okay, we saw one aspect of this plugin: getting all our images from our assets directory. However, sometimes we want to be a little more precise.

Each `.md` file starts what looks like some funny looking text. That is actually YAML for that file. It looks like the following:

    ---
    Title: Animal Pictures
    Description: Animal Pictures
    Author: Lloyd Sargent
    Date: 2019-03-17
    Robots:
    Template: animal-pics-index
    selected_assets: assets/2016
    ---

The YAML option `selected_assets` goes to the folder where (in our instance) we have a lot of animal pictures from 2016.

A slight modification to our Twig gives us the following:

    {% for asset in selected_assets %}
        <img src="{{ base_url }}/{{ asset }}">
    {% endfor %}

Instead of being an array of ALL our images, it goes to the directory `assets/2016` and creates an array of all those images and stores that array in `selected_assets`. So now, rather than listing all the images in a directory in my `.md` file, I only need specify the directory. As I add images, it cleverly figures out to add them. It even sorts them so if I don't want them higgledy-piggly, they will come out all ordered!

### Wait, This Looks Like I Could Implement a Lightbox...

Yes, that is exactly what it was designed for and it comes at a low-low price of free.
