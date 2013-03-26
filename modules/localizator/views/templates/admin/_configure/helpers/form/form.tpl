{extends file="helpers/form/form.tpl"}
{block name="leadin"}
		<fieldset style="width: 300px;float:right;margin-left:15px;margin-top:10px;">
			<legend><img src="../img/admin/manufacturers.gif"/> {l s='Information' mod='localizator'}</legend>
			<div id="dev_div">
				<span><strong>{l s='Version' mod='localizator'}: </strong>{$version}</span><br>
				<span><strong>{l s='License' mod='localizator'}:</strong> <a class="link" href="http://www.opensource.org/licenses/osl-3.0.php" target="_blank">OSL 3.0</a></span><br>
				<span><strong>{l s='Developer' mod='localizator'}:</strong> <a class="link" href="mailto:admin@prestalab.ru" target="_blank">{$author}</a><br>
				<span><strong>{l s='Description' mod='localizator'}:</strong> <a class="link" href="http://prestalab.ru/" target="_blank">PrestaLab.ru</a><br>
				<p style="text-align:center"><a href="http://prestalab.ru/"><img src="{$this_path}banner.png" alt="{l s='Modules and Templates for PrestaShop' mod='localizator'}"/></a></p>
			</div>
		</fieldset>
{/block}