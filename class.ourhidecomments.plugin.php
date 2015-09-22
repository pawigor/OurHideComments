<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2008, 2009 Vanilla Forums Inc.
This file is part of Garden.
Garden is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
Garden is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Garden.  If not, see <http://www.gnu.org/licenses/>.
Contact Vanilla Forums Inc. at support [at] vanillaforums [dot] com
*/

// Define the plugin:
$PluginInfo['OurHideComments'] = array(
    'Description' => 'Provides an example Development Pattern for Vanilla 2 plugins by demonstrating how to insert discussion body excerpts into the discussions list.',
    'Version' => '1.0',
    'RequiredApplications' => array('Vanilla' => '2.0.10'),
    'RequiredTheme' => FALSE,
    'RequiredPlugins' => FALSE,
    'HasLocale' => FALSE,
    'SettingsUrl' => '/plugin/ourhidecomments',
    'SettingsPermission' => 'Garden.AdminUser.Only',
    'Author' => "Tim Gunter",
    'AuthorEmail' => 'tim@vanillaforums.com',
    'AuthorUrl' => 'http://www.vanillaforums.com'
);

class OurHideCommentsPlugin extends Gdn_Plugin
{

    /**
     * Plugin constructor
     *
     * This fires once per page load, during execution of bootstrap.php. It is a decent place to perform
     * one-time-per-page setup of the plugin object. Be careful not to put anything too strenuous in here
     * as it runs every page load and could slow down your forum.
     */
    public function __construct()
    {

    }

    /**
     * Base_Render_Before Event Hook
     *
     * This is a common hook that fires for all controllers (Base), on the Render method (Render), just
     * before execution of that method (Before). It is a good place to put UI stuff like CSS and Javascript
     * inclusions. Note that all the Controller logic has already been run at this point.
     *
     * @param $Sender Sending controller instance
     */
    public function Base_Render_Before($Sender)
    {
//        $Sender->AddCssFile($this->GetResource('design/ourhidecomments.css', FALSE, FALSE));
//        $Sender->AddJsFile($this->GetResource('js/ourhidecomments.js', FALSE, FALSE));
    }

    /**
     * Because code ahead don't working
     * @param $Sender
     */
    public function DiscussionController_Render_Before(&$Sender)
    {
        $Sender->AddJsFile('ourhidecomments.js', "plugins/OurHideComments/js");
        $Sender->AddCssFile('ourhidecomments.css', "plugins/OurHideComments/design");
    }

    /**
     * Create a method called "Example" on the PluginController
     *
     * One of the most powerful tools at a plugin developer's fingertips is the ability to freely create
     * methods on other controllers, effectively extending their capabilities. This method creates the
     * Example() method on the PluginController, effectively allowing the plugin to be invoked via the
     * URL: http://www.yourforum.com/plugin/Example/
     *
     * From here, we can do whatever we like, including turning this plugin into a mini controller and
     * allowing us an easy way of creating a dashboard settings screen.
     *
     * @param $Sender Sending controller instance
     */
    public function PluginController_OurHideComments_Create($Sender)
    {
        /*
         * If you build your views properly, this will be used as the <title> for your page, and for the header
         * in the dashboard. Something like this works well: <h1><?php echo T($this->Data['Title']); ?></h1>
         */
        $Sender->Title('OurHideComments');
        $Sender->AddSideMenu('plugin/ourhidecomments');

        // If your sub-pages use forms, this is a good place to get it ready
        $Sender->Form = new Gdn_Form();

        /*
         * This method does a lot of work. It allows a single method (PluginController::Example() in this case)
         * to "forward" calls to internal methods on this plugin based on the URL's first parameter following the
         * real method name, in effect mimicing the functionality of as a real top level controller.
         *
         * For example, if we accessed the URL: http://www.yourforum.com/plugin/Example/test, Dispatch() here would
         * look for a method called ExamplePlugin::Controller_Test(), and invoke it. Similarly, we we accessed the
         * URL: http://www.yourforum.com/plugin/Example/foobar, Dispatch() would find and call
         * ExamplePlugin::Controller_Foobar().
         *
         * The main benefit of this style of extending functionality is that all of a plugin's external API is
         * consolidated under one namespace, reducing the chance for random method name conflicts with other
         * plugins.
         *
         * Note: When the URL is accessed without parameters, Controller_Index() is called. This is a good place
         * for a dashboard settings screen.
         */
        $this->Dispatch($Sender, $Sender->RequestArgs);
    }

    public function Controller_Index($Sender)
    {
        // Prevent non-admins from accessing this page
        $Sender->Permission('Vanilla.Settings.Manage');

        $Sender->SetData('PluginDescription', $this->GetPluginKey('Description'));

        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->SetField(array(
//            'Plugin.Example.RenderCondition' => 'all',
//            'Plugin.Example.TrimSize' => 100
        ));

        // Set the model on the form.
        $Sender->Form->SetModel($ConfigurationModel);

        // If seeing the form for the first time...
        if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
            // Apply the config settings to the form.
            $Sender->Form->SetData($ConfigurationModel->Data);
        } else {
//            $ConfigurationModel->Validation->ApplyRule('Plugin.Example.RenderCondition', 'Required');

//            $ConfigurationModel->Validation->ApplyRule('Plugin.Example.TrimSize', 'Required');
//            $ConfigurationModel->Validation->ApplyRule('Plugin.Example.TrimSize', 'Integer');

            $Saved = $Sender->Form->Save();
            if ($Saved) {
                $Sender->StatusMessage = T("Your changes have been saved.");
            }
        }

        // GetView() looks for files inside plugins/PluginFolderName/views/ and returns their full path. Useful!
        $Sender->Render($this->GetView('ourhidecomments.php'));
    }

    /**
     * Add a link to the dashboard menu
     *
     * By grabbing a reference to the current SideMenu object we gain access to its methods, allowing us
     * to add a menu link to the newly created /plugin/Example method.
     *
     * @param $Sender Sending controller instance
     */
    public function Base_GetAppSettingsMenuItems_Handler($Sender)
    {
        $Menu = &$Sender->EventArguments['SideMenu'];
        $Menu->AddLink('Add-ons', 'OurHideComments', 'plugin/ourhidecomments', 'Garden.AdminUser.Only');
    }

    /**
     * @param $Sender DiscussionController
     * @return bool|void
     */
    public function DiscussionController_HideComment_Create($Sender)
    {
        // TODO write your code here.
        if (sizeof($Arguments = $Sender->RequestArgs) != 1)
            return;
        list($CommentID) = $Arguments;
        $isHidden = $this->Hidden($CommentID);
        if (!$isHidden) {
            Gdn::SQL()
                ->insert("HiddenComment", array('CommentID' => $CommentID));
            $Sender->JsonTarget("#Comment_{$CommentID}", 'HiddenComment', 'AddClass'); // Add the class
            $Sender->JsonTarget("#Comment_{$CommentID}", 'updateHidden', 'Callback');
        } else {
            Gdn::SQL()
                ->delete('HiddenComment', array('CommentID' => $CommentID));
            $Sender->JsonTarget("#Comment_{$CommentID}", 'HiddenComment', 'RemoveClass'); // Remove the class
            $Sender->JsonTarget("#Comment_{$CommentID}", 'updateShown', 'Callback');
        }
//        return true;
        $Sender->Render('Blank', 'Utility', 'Dashboard');
    }

    /**
     * @param $CommentID
     * @return bool
     */
    private function Hidden($CommentID)
    {
        $Result = Gdn::SQL()
            ->select("CommentId")
            ->from("HiddenComment")
            ->where('CommentID', $CommentID)
            ->get()
            ->firstRow();
        if ($Result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * Apply Hidden preferences to comments.
     * Functionality derived from Ignore plugin by Tim Gunter
     *
     * @param mixed $Sender The Sender data structure.
     */
    public function DiscussionController_BeforeCommentDisplay_Handler($Sender)
    {
        // Signed-in users only
        $str = 'HiddenCommentHide';
        $userID = Gdn::Session()->UserID;
        if ($userID) {
            if (Gdn::UserModel()->getID($userID)->Admin) {
                $str = 'HiddenCommentAdmin';
            }
        } else {
            return;
        }
        // Get our DiscussionID and our CommentID
        $DiscussionID = $Sender->EventArguments['Discussion']->DiscussionID;
        $CommentID = $Sender->EventArguments['Comment']->CommentID;
        if ($this->Hidden($CommentID)) {
            $Classes = explode(" ", $Sender->EventArguments['CssClass']);
            $Classes[] = 'HiddenComment';
            $Classes[] = $str;
            $Classes = array_fill_keys($Classes, NULL);
            $Classes = implode(' ', array_keys($Classes));
            $Sender->EventArguments['CssClass'] = $Classes;
        }
    }

    /**
     * Insert the Hide option in the comment options menu.
     * @param mixed $Sender The Sender data structure
     * @param mixed $Args The Event Arguments.
     */
    public function DiscussionController_CommentOptions_Handler($Sender, $Args)
    {
        if (!Gdn::Session()->UserID)
            return;
        $CommentID = $Args['Comment']->CommentID;
        // Check to see if the user can hide this comment. If it's already hidden, allow them to unhide it.
//        if (!$this->CanHide($DiscussionID, $CommentID) && $this->Hidden($DiscussionID, $CommentID) === false)
//            return;
        if ($this->canHide())
            $this->MenuOptions($Args['CommentOptions'], $CommentID);
    }

    /**
     * @return bool
     */
    private function canHide()
    {
        if (!($UserID = Gdn::Session()->UserID)) {
            return false;
        };
        return Gdn::UserModel()->checkPermission($UserID, 'Conversations.Moderation.Manage');
    }

    /**
     * Generate a menu option for Hiding
     * @param mixed $Options The array of options to insert this option into.
     * @param mixed $CommentID The comment ID inside the discussion, or NULL if the target is the discussion.
     * @param mixed $Key Array key to use for this specific option.
     * @internal param mixed $DiscussionID The Discussion ID of the discussion the action takes place in.
     */
    public function MenuOptions(&$Options, $CommentID, $Key = NULL)
    {
        // Set up our label to show current state of Hide preference.
        $Label = "Hide ";
        if ($this->Hidden($CommentID))
            $Label = "Unhide ";

        $Label .= "Comment";

        // Build our CSS Class name based on the action
        $CssClass = 'Hide' . (($CommentID == NULL) ? "Discussion" : "Comment");

        // Create a unique CSS Class for live page updating.
        $UniqueCss = $CssClass . '_' . $CommentID;

        // URL of our Ajax request. Use 'd' as a URL-safe alternative for Null
        $Url = "/discussion/hidecomment/" . $CommentID;

        // Set up our option entry
        $Options[$Key] = array(
            'Label' => T($Label),
            'Url' => $Url,
            'Class' => $CssClass . ' Hijack ' . $UniqueCss
        );
    }

    /**
     * Plugin setup
     *
     * This method is fired only once, immediately after the plugin has been enabled in the /plugins/ screen,
     * and is a great place to perform one-time setup tasks, such as database structure changes,
     * addition/modification ofconfig file settings, filesystem changes, etc.
     */
    public function Setup()
    {

        // Set up the plugin's default values
//        SaveToConfig('Plugin.OurHideComments.TrimSize', 100);
//        SaveToConfig('Plugin.OurHideComments.RenderCondition', "all");


        Gdn::Structure()->Table('HiddenComment')->primaryKey('CommentID')->Set(FALSE, FALSE);
        /*
        // Create table GDN_Example, if it doesn't already exist
        Gdn::Structure()
           ->Table('Example')
           ->PrimaryKey('ExampleID')
           ->Column('Name', 'varchar(255)')
           ->Column('Type', 'varchar(128)')
           ->Column('Size', 'int(11)')
           ->Column('InsertUserID', 'int(11)')
           ->Column('DateInserted', 'datetime')
           ->Column('ForeignID', 'int(11)', TRUE)
           ->Column('ForeignTable', 'varchar(24)', TRUE)
           ->Set(FALSE, FALSE);
        */
    }

    /**
     * Plugin cleanup
     *
     * This method is fired only once, immediately before the plugin is disabled, and is a great place to
     * perform cleanup tasks such as deletion of unsued files and folders.
     */
    public function OnDisable()
    {
//        RemoveFromConfig('Plugin.Example.TrimSize');
//        RemoveFromConfig('Plugin.Example.RenderCondition');
    }

}
