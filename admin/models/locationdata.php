<?php
/*--------------------------------------------------------------------------------------------------------|  www.vdm.io  |------/
    __      __       _     _____                 _                                  _     __  __      _   _               _
    \ \    / /      | |   |  __ \               | |                                | |   |  \/  |    | | | |             | |
     \ \  / /_ _ ___| |_  | |  | | _____   _____| | ___  _ __  _ __ ___   ___ _ __ | |_  | \  / | ___| |_| |__   ___   __| |
      \ \/ / _` / __| __| | |  | |/ _ \ \ / / _ \ |/ _ \| '_ \| '_ ` _ \ / _ \ '_ \| __| | |\/| |/ _ \ __| '_ \ / _ \ / _` |
       \  / (_| \__ \ |_  | |__| |  __/\ V /  __/ | (_) | |_) | | | | | |  __/ | | | |_  | |  | |  __/ |_| | | | (_) | (_| |
        \/ \__,_|___/\__| |_____/ \___| \_/ \___|_|\___/| .__/|_| |_| |_|\___|_| |_|\__| |_|  |_|\___|\__|_| |_|\___/ \__,_|
                                                        | |                                                                 
                                                        |_| 				
/-------------------------------------------------------------------------------------------------------------------------------/

	@version		1.0.1
	@build			2nd February, 2017
	@created		28th June, 2016
	@package		Location Data
	@subpackage		locationdata.php
	@author			Llewellyn van der Merwe <https://www.vdm.io/>	
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html 
	
	You can via an url receive all the data related to any given IP address in a Json object. 
                                                             
/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the Joomla modellist library
jimport('joomla.application.component.modellist');
jimport('joomla.application.component.helper');

/**
 * Locationdata Model
 */
class LocationdataModelLocationdata extends JModelList
{
	public function getIcons()
	{
                // load user for access menus
                $user = JFactory::getUser();
                // reset icon array
		$icons  = array();
                // view groups array
		$viewGroups = array(
			'main' => array('png.countries', 'png.currencies', 'png.exchange_rates', 'png.ip_tables', 'png.updatedata')
		);
		// view access array
		$viewAccess = array(
			'updatedata.submenu' => 'updatedata.submenu',
			'updatedata.dashboard_list' => 'updatedata.dashboard_list',
			'country.create' => 'country.create',
			'countries.access' => 'country.access',
			'country.access' => 'country.access',
			'countries.submenu' => 'country.submenu',
			'countries.dashboard_list' => 'country.dashboard_list',
			'currency.create' => 'currency.create',
			'currencies.access' => 'currency.access',
			'currency.access' => 'currency.access',
			'currencies.submenu' => 'currency.submenu',
			'currencies.dashboard_list' => 'currency.dashboard_list',
			'exchange_rate.create' => 'exchange_rate.create',
			'exchange_rates.access' => 'exchange_rate.access',
			'exchange_rate.access' => 'exchange_rate.access',
			'exchange_rates.submenu' => 'exchange_rate.submenu',
			'exchange_rates.dashboard_list' => 'exchange_rate.dashboard_list',
			'ip_table.create' => 'ip_table.create',
			'ip_tables.access' => 'ip_table.access',
			'ip_table.access' => 'ip_table.access',
			'ip_tables.submenu' => 'ip_table.submenu',
			'ip_tables.dashboard_list' => 'ip_table.dashboard_list');
		foreach($viewGroups as $group => $views)
                {
			$i = 0;
			if (LocationdataHelper::checkArray($views))
                        {
				foreach($views as $view)
				{
					$add = false;
					if (strpos($view,'.') !== false)
                                        {
                                                $dwd = explode('.', $view);
                                                if (count($dwd) == 3)
                                                {
                                                        list($type, $name, $action) = $dwd;
                                                }
                                                elseif (count($dwd) == 2)
                                                {
                                                        list($type, $name) = $dwd;
                                                        $action = false;
                                                }
                                                if ($action)
                                                {
                                                        $viewName = $name;
                                                        switch($action)
                                                        {
                                                                case 'add':
                                                                        $url 	='index.php?option=com_locationdata&view='.$name.'&layout=edit';
                                                                        $image 	= $name.'_'.$action.'.'.$type;
                                                                        $alt 	= $name.'&nbsp;'.$action;
                                                                        $name	= 'COM_LOCATIONDATA_DASHBOARD_'.LocationdataHelper::safeString($name,'U').'_ADD';
                                                                        $add	= true;
                                                                break;
                                                                default:
                                                                        $url 	= 'index.php?option=com_categories&view=categories&extension=com_locationdata.'.$name;
                                                                        $image 	= $name.'_'.$action.'.'.$type;
                                                                        $alt 	= $name.'&nbsp;'.$action;
                                                                        $name	= 'COM_LOCATIONDATA_DASHBOARD_'.LocationdataHelper::safeString($name,'U').'_'.LocationdataHelper::safeString($action,'U');
                                                                break;
                                                        }
                                                }
                                                else
                                                {
                                                        $viewName 	= $name;
                                                        $alt 		= $name;
                                                        $url 		= 'index.php?option=com_locationdata&view='.$name;
                                                        $image 		= $name.'.'.$type;
                                                        $name 		= 'COM_LOCATIONDATA_DASHBOARD_'.LocationdataHelper::safeString($name,'U');
                                                        $hover		= false;
                                                }
                                        }
                                        else
                                        {
                                                $viewName 	= $view;
                                                $alt 		= $view;
                                                $url 		= 'index.php?option=com_locationdata&view='.$view;
                                                $image 		= $view.'.png';
                                                $name 		= ucwords($view).'<br /><br />';
                                                $hover		= false;
                                        }
                                        // first make sure the view access is set
                                        if (LocationdataHelper::checkArray($viewAccess))
                                        {
						// setup some defaults
						$dashboard_add = false;
						$dashboard_list = false;
                                                $accessTo = '';
                                                $accessAdd = '';
                                                // acces checking start
                                                $accessCreate = (isset($viewAccess[$viewName.'.create'])) ? LocationdataHelper::checkString($viewAccess[$viewName.'.create']):false;
                                                $accessAccess = (isset($viewAccess[$viewName.'.access'])) ? LocationdataHelper::checkString($viewAccess[$viewName.'.access']):false;
						// set main controllers
						$accessDashboard_add = (isset($viewAccess[$viewName.'.dashboard_add'])) ? LocationdataHelper::checkString($viewAccess[$viewName.'.dashboard_add']):false;
						$accessDashboard_list = (isset($viewAccess[$viewName.'.dashboard_list'])) ? LocationdataHelper::checkString($viewAccess[$viewName.'.dashboard_list']):false;
                                                // check for adding access
                                                if ($add && $accessCreate)
                                                {
                                                        $accessAdd = $viewAccess[$viewName.'.create'];
                                                }
                                                elseif ($add)
                                                {
                                                        $accessAdd = 'core.create';
                                                }
                                                // check if acces to view is set
                                                if ($accessAccess)
                                                {
                                                        $accessTo = $viewAccess[$viewName.'.access'];
                                                }
						// set main access controllers
						if ($accessDashboard_add)
						{
							$dashboard_add	= $user->authorise($viewAccess[$viewName.'.dashboard_add'], 'com_locationdata');
						}
						if ($accessDashboard_list)
						{
							$dashboard_list = $user->authorise($viewAccess[$viewName.'.dashboard_list'], 'com_locationdata');
						}
                                                if (LocationdataHelper::checkString($accessAdd) && LocationdataHelper::checkString($accessTo))
                                                {
                                                        // check access
                                                        if($user->authorise($accessAdd, 'com_locationdata') && $user->authorise($accessTo, 'com_locationdata') && $dashboard_add)
                                                        {
                                                                $icons[$group][$i]              = new StdClass;
                                                                $icons[$group][$i]->url 	= $url;
                                                                $icons[$group][$i]->name 	= $name;
                                                                $icons[$group][$i]->image 	= $image;
                                                                $icons[$group][$i]->alt 	= $alt;
                                                        }
                                                }
                                                elseif (LocationdataHelper::checkString($accessTo))
                                                {
                                                        // check access
                                                        if($user->authorise($accessTo, 'com_locationdata') && $dashboard_list)
                                                        {
                                                                $icons[$group][$i]              = new StdClass;
                                                                $icons[$group][$i]->url 	= $url;
                                                                $icons[$group][$i]->name 	= $name;
                                                                $icons[$group][$i]->image 	= $image;
                                                                $icons[$group][$i]->alt 	= $alt;
                                                        }
                                                }
                                                elseif (LocationdataHelper::checkString($accessAdd))
                                                {
                                                        // check access
                                                        if($user->authorise($accessAdd, 'com_locationdata') && $dashboard_add)
                                                        {
                                                                $icons[$group][$i]              = new StdClass;
                                                                $icons[$group][$i]->url 	= $url;
                                                                $icons[$group][$i]->name 	= $name;
                                                                $icons[$group][$i]->image 	= $image;
                                                                $icons[$group][$i]->alt 	= $alt;
                                                        }
                                                }
                                                else
                                                {
                                                        $icons[$group][$i]              = new StdClass;
                                                        $icons[$group][$i]->url 	= $url;
                                                        $icons[$group][$i]->name 	= $name;
                                                        $icons[$group][$i]->image 	= $image;
                                                        $icons[$group][$i]->alt 	= $alt;
                                                }
                                        }
                                        else
                                        {
                                                $icons[$group][$i]              = new StdClass;
                                                $icons[$group][$i]->url 	= $url;
                                                $icons[$group][$i]->name 	= $name;
                                                $icons[$group][$i]->image 	= $image;
                                                $icons[$group][$i]->alt 	= $alt;
                                        }
                                        $i++;
                                }
                        }
                        else
                        {
                                $icons[$group][$i] = false;
			}
		}
		return $icons;
	}

	public function getGithub()
	{
		$document = JFactory::getDocument();
		$document->addScript(JURI::root() . "media/com_locationdata/js/marked.js");
		$document->addScriptDeclaration('
		var token = "'.JSession::getFormToken().'";
		var urlToGetAllOpenIssues = "https://api.github.com/repos/vdm-io/Joomla-Location-Data/issues?state=open&page=1&per_page=5";
		var urlToGetAllClosedIssues = "https://api.github.com/repos/vdm-io/Joomla-Location-Data/issues?state=closed&page=1&per_page=5";
		jQuery(document).ready(function () {
			jQuery.getJSON(urlToGetAllOpenIssues, function (openissues) {
				jQuery("#openissues").html("");
				jQuery.each(openissues, function (i, issue) {
					jQuery("#openissues")
            				.append("<h3><a href=\"" + issue.html_url + "\" target=\"_blank\">" + issue.title + "</a></h3>")
            				.append("<small><em>#" + issue.number + " '.JText::_('COM_LOCATIONDATA_OPENED_BY').' " + issue.user.login + "<em></small>")
            				.append(marked(issue.body))
            				.append("<a href=\"" + issue.html_url + "\" target=\"_blank\">'.JText::_('COM_LOCATIONDATA_RESPOND_TO_THIS_ISSUE_ON_GITHUB').'</a>...<hr />");
    				});
			});
			jQuery.getJSON(urlToGetAllClosedIssues, function (closedissues) {
				jQuery("#closedissues").html("");
				jQuery.each(closedissues, function (i, issue) {
					jQuery("#closedissues")
            				.append("<h3><a href=\"" + issue.html_url + "\" target=\"_blank\">" + issue.title + "</a></h3>")
            				.append("<small><em>#" + issue.number + " '.JText::_('COM_LOCATIONDATA_OPENED_BY').' " + issue.user.login + "<em></small>")
            				.append(marked(issue.body))
            				.append("<a href=\"" + issue.html_url + "\" target=\"_blank\">'.JText::_('COM_LOCATIONDATA_REVIEW_THIS_ISSUE_ON_GITHUB').'</a>...<hr />");
    				});
			});
		});
		// to check is READ/NEW
		function getIS(type,notice){
			if(type == 1){
				var getUrl = "index.php?option=com_locationdata&task=ajax.isNew&format=json";
			} else if (type == 2) {
				var getUrl = "index.php?option=com_locationdata&task=ajax.isRead&format=json";
			}	
			if(token.length > 0 && notice.length){
				var request = "token="+token+"&notice="+notice;
			}
			return jQuery.ajax({
				type: "POST",
				url: getUrl,
				dataType: "jsonp",
				data: request,
				jsonp: "callback"
			});
		}
		// nice little dot trick :)
		jQuery(document).ready( function($) {
			var x=0;
			setInterval(function() {
				var dots = "";
				x++;
				for (var y=0; y < x%8; y++) {
					dots+=".";
				}
				$(".loading-dots").text(dots);
			} , 500);
		});');
		$create = '<div class="btn-group pull-right">
					<a href="https://github.com/vdm-io/Joomla-Location-Data/issues/new" class="btn btn-primary"  target="_blank">'.JText::_('COM_LOCATIONDATA_NEW_ISSUE').'</a>
				</div></br >';
		$moreopen = '<b><a href="https://github.com/vdm-io/Joomla-Location-Data/issues" target="_blank">'.JText::_('COM_LOCATIONDATA_VIEW_MORE_ISSUES_ON_GITHUB').'</a>...</b>';
		$moreclosed = '<b><a href="https://github.com/vdm-io/Joomla-Location-Data/issues?q=is%3Aissue+is%3Aclosed" target="_blank">'.JText::_('COM_LOCATIONDATA_VIEW_MORE_ISSUES_ON_GITHUB').'</a>...</b>';

		return (object) array(
				'openissues' => $create.'<div id="openissues">'.JText::_('COM_LOCATIONDATA_A_FEW_OPEN_ISSUES_FROM_GITHUB_IS_LOADING').'.<span class="loading-dots">.</span></small></div>'.$moreopen, 
				'closedissues' => $create.'<div id="closedissues">'.JText::_('COM_LOCATIONDATA_A_FEW_CLOSED_ISSUES_FROM_GITHUB_IS_LOADING').'.<span class="loading-dots">.</span></small></div>'.$moreclosed
		);
	}

	public function getReadme()
	{
		$document = JFactory::getDocument();
		$document->addScriptDeclaration('
		var getreadme = "'. JURI::root() . 'administrator/components/com_locationdata/README.txt";
		jQuery(document).ready(function () {
			jQuery.get(getreadme)
			.success(function(readme) { 
				jQuery("#readme-md").html(marked(readme));
			})
			.error(function(jqXHR, textStatus, errorThrown) { 
				jQuery("#readme-md").html("'.JText::_('COM_LOCATIONDATA_PLEASE_CHECK_AGAIN_LATTER').'");
			});
		});');

		return '<div id="readme-md">'.JText::_('COM_LOCATIONDATA_THE_README_IS_LOADING').'.<span class="loading-dots">.</span></small></div>';
	}

	public function getWiki()
	{
		$document = JFactory::getDocument();
		$document->addScriptDeclaration('
		var gewiki = "https://raw.githubusercontent.com/wiki/vdm-io/Joomla-Location-Data/Home.md";
		jQuery(document).ready(function () {
			jQuery.get(gewiki)
			.success(function(wiki) { 
				jQuery("#wiki-md").html(marked(wiki));
			})
			.error(function(jqXHR, textStatus, errorThrown) { 
				jQuery("#wiki-md").html("'.JText::_('COM_LOCATIONDATA_PLEASE_CHECK_AGAIN_LATTER').'");
			});
		});');

		return '<div id="wiki-md">'.JText::_('COM_LOCATIONDATA_THE_WIKI_IS_LOADING').'.<span class="loading-dots">.</span></small></div>';
	}

	public function getNoticeboard()
	{
		$document = JFactory::getDocument();
		$document->addScriptDeclaration('
		var noticeboard = "https://www.vdm.io/locationdata-noticeboard-md";
		jQuery(document).ready(function () {
			jQuery.get(noticeboard)
			.success(function(board) { 
				if (board.length > 5) {
					jQuery("#noticeboard-md").html(marked(board));
					getIS(1,board).done(function(result) {
						if (result){
							jQuery("#cpanel_tabTabs a").each(function() {
								if (this.href.indexOf("#vast_development_method") >= 0) {
									var textVDM = jQuery(this).text();
									jQuery(this).html("<span class=\"label label-important vdm-new-notice\">1</span> "+textVDM);
									jQuery(this).attr("id","vdm-new-notice");
									jQuery("#vdm-new-notice").click(function() {
										getIS(2,board).done(function(result) {
												if (result) {
												jQuery(".vdm-new-notice").fadeOut(500);
											}
										});
									});
								}
							});
						}
					});
				} else {
					jQuery("#noticeboard-md").html("'.JText::_('COM_LOCATIONDATA_ALL_IS_GOOD_PLEASE_CHECK_AGAIN_LATTER').'");
				}
			})
			.error(function(jqXHR, textStatus, errorThrown) { 
				jQuery("#noticeboard-md").html("'.JText::_('COM_LOCATIONDATA_ALL_IS_GOOD_PLEASE_CHECK_AGAIN_LATTER').'");
			});
		});');

		return '<div id="noticeboard-md">'.JText::_('COM_LOCATIONDATA_THE_NOTICE_BOARD_IS_LOADING').'.<span class="loading-dots">.</span></small></div>';
	}
}
