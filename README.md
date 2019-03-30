## grabassets

GrabAssets is a PicoCMS plugin designed to do one thing and one thing only: get you a list of all your assets in a nice big array.

### Installation

Installation is easy. Clone GrabAssets into your PicoCMS folder. It consists of a single PHP file named, appropriately enough, `GrabAssets.php`.

That's it!

### What it does

When you load a page, GrabAssets iterates through your assets folder and creates an array names `array_of_assets` that is accessible via Twig. For example, if you want a page full of images, you could do something like the followiing:


