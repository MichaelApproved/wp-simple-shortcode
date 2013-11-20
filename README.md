wp-simple-shortcode
===================

A Simple WordPress Shortcode Creator Drop-in Plugin

Apply additional parameters to the shortcode by simple adding %parameter_name% to the shortcode replacement block


----------


Example:
--------

This is a string with a %%var1:default-var%% as well as another %%var2:default-var-2%% or any kind of %%crazy_variable_name:default-crazy%% that you would like.

Inside the Post:
----------------

    [shortcode_label var1="asdf"]

Would Produce...

    This is a string with a asdf as well as another default-var-2 or any kind of default-crazy that you would like.