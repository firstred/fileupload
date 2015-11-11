{*
* 2015 Michael Dekker
*
*  @author    Michael Dekker <prestashopaddons@michaeldekker.com>
*  @copyright 2015 Michael Dekker
*  @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*}
{if $smarty.const._PS_VERSION_|@addcslashes:'\'' < '1.6'}
<fieldset>
	<h4> {l s='Donate' mod='fileupload'}</h4>
	<p>
		{l s='If this module helped you, please make a donation to support further development.' mod='fileupload'}<br />
	</p>
	<a href="https://www.paypal.me/MDekker" target="_blank">
		<img alt="" border="0" src="https://www.paypalobjects.com/{l s='en_US' mod='fileupload'}/i/btn/btn_donateCC_LG.gif">
	</a>
	<h4>{l s='Contact' mod='fileupload'}</h4>
	<p>
		{l s='There is a topic dedicated to this module on PrestaShop\'s forum:' mod='fileupload'}
	</p>
	<a href="https://www.prestashop.com/forums/topic/456742-free-module-file-upload-let-customers-upload-other-files-besides-images/" target="_blank">
		https://www.prestashop.com/forums/topic/456742-free-module-file-upload-let-customers-upload-other-files-besides-images/
	</a>
</fieldset>
{else}
<div class="panel">
	<h3><i class="icon icon-info"></i> {l s='Miscellaneous' mod='fileupload'}</h3>
	<h4>{l s='Donate' mod='fileupload'}</h4>
	<p>
		{l s='If this module helped you, please make a donation to support further development.' mod='fileupload'}
	</p>
	<a href="https://www.paypal.me/MDekker" target="_blank">
		<img alt="" border="0" src="https://www.paypalobjects.com/{l s='en_US' mod='fileupload'}/i/btn/btn_donateCC_LG.gif">
	</a>
	<h4>{l s='Contact' mod='fileupload'}</h4>
	<p>
		{l s='There is a topic dedicated to this module on PrestaShop\'s forum:' mod='fileupload'}
	</p>
	<a href="https://www.prestashop.com/forums/topic/456742-free-module-file-upload-let-customers-upload-other-files-besides-images/" target="_blank">
		https://www.prestashop.com/forums/topic/456742-free-module-file-upload-let-customers-upload-other-files-besides-images/
	</a>
</div>
{/if}