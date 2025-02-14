{if $IN_CROP}
{html_style}
.cropImage-ratio label {
	margin-right:10px;
}
{/html_style}

{combine_css path="themes/default/js/plugins/jquery.Jcrop.css"}
{combine_script id='jquery.jcrop' load='footer' require='jquery' path='themes/default/js/plugins/jquery.Jcrop.min.js'}

{footer_script require="jquery"}
var jcrop_api;

jQuery("#jcrop").Jcrop({ldelim}
		//boxWidth: {$crop.display_width}, 
    //boxHeight: {$crop.display_height},
		boxWidth: 500, boxHeight: 400,
    onChange: jOnChange,
    onRelease: jOnRelease
	},
  function(){ldelim}
    jcrop_api = this;
  });

	$("input[name='ratio']").change(function(e) {ldelim}
      if ($("input[name='ratio']:checked").val() == '1/1')
			{ldelim}
			  jcrop_api.setOptions({ldelim} aspectRatio: 1/1 });
				document.getElementById('ratioC').disabled = true;
				document.getElementById('ratioC').value = '';
			}
			else if ($("input[name='ratio']:checked").val() == '4/5')
			{ldelim}
			  jcrop_api.setOptions({ldelim} aspectRatio: 4/5 });
				document.getElementById('ratioC').disabled = true;
				document.getElementById('ratioC').value = '';
			}
			else if ($("input[name='ratio']:checked").val() == '4/3')
			{ldelim}
			  jcrop_api.setOptions({ldelim} aspectRatio: 4/3 });
				document.getElementById('ratioC').disabled = true;
				document.getElementById('ratioC').value = '';
			}
			else if ($("input[name='ratio']:checked").val() == '5/7')
			{ldelim}
			  jcrop_api.setOptions({ldelim} aspectRatio: 5/7 });
				document.getElementById('ratioC').disabled = true;
				document.getElementById('ratioC').value = '';
			}
			else if ($("input[name='ratio']:checked").val() == '16/9')
			{ldelim}
				jcrop_api.setOptions({ldelim} aspectRatio: 16/9 });
				document.getElementById('ratioC').disabled = true;
				document.getElementById('ratioC').value = '';
			}
			else if ($("input[name='ratio']:checked").val() == '2.35/1')
			{ldelim}
				jcrop_api.setOptions({ldelim} aspectRatio: 2.35/1 });
				document.getElementById('ratioC').disabled = true;
				document.getElementById('ratioC').value = '';
			}
			else if ($("input[name='ratio']:checked").val() == 'original')
			{ldelim}
				jcrop_api.setOptions({ldelim} aspectRatio: eval($('#image_ratio').val()) });
				document.getElementById('ratioC').disabled = true;
				document.getElementById('ratioC').value = '';				
			}
			else if ($("input[name='ratio']:checked").val() == 'C')
			{ldelim}
				document.getElementById('ratioC').disabled = false;
				if ( eval($('#ratioC').val()) > 0 )
				jcrop_api.setOptions({ldelim} aspectRatio: eval($('#ratioC').val()) });
			}
			else
			{ldelim}
			  jcrop_api.setOptions({ldelim} aspectRatio: 0 });
				document.getElementById('ratioC').disabled = true;
				document.getElementById('ratioC').value = '';
			}
      jcrop_api.focus();
    });
	$("input[name='ratioC']").change(function(e) {ldelim}
			if (($("input[name='ratio']:checked").val() == 'C') && eval($('#ratioC').val()) > 0)
			  jcrop_api.setOptions({ldelim} aspectRatio: eval($('#ratioC').val()) });
      jcrop_api.focus();
    });	
  
function jOnChange(sel) {ldelim}
	jQuery("input[name='x']").val(sel.x);
	jQuery("input[name='y']").val(sel.y);
	jQuery("input[name='x2']").val(sel.x2);
	jQuery("input[name='y2']").val(sel.y2);
	
  jQuery("input[name='w']").val(sel.w);
	jQuery("input[name='h']").val(sel.h);	
	
  jQuery("#width").html(Math.round(sel.x2-sel.x).toFixed(0));
  jQuery("#height").html(Math.round(sel.y2-sel.y).toFixed(0));
}
  function jOnRelease() {ldelim}
	
}

{/footer_script}

<form method="post" action="">
<fieldset>
  <legend>{'Crop Photo'|@translate}</legend>
  {'Choose the part of the photo you want to keep'|@translate}<br>
  <img id="jcrop" src="{$picture.banner_src}?{$random_avoid_cache_key}" width="{$crop.display_width}" height="{$crop.display_height}">
  
  <ul>
    <li><b>{'Width'|@translate}:</b> <span id="width"></span>px</li>
    <li><b>{'Height'|@translate}:</b> <span id="height"></span>px</li>
  </ul>
	
  <input type="hidden" name="x">
  <input type="hidden" name="y">
  <input type="hidden" name="x2">
  <input type="hidden" name="y2">
	<input type="hidden" name="h">
	<input type="hidden" name="w">
  <input type="hidden" name="picture_file" value="{$picture.path}">
	<input type="hidden" name="image_ratio" id="image_ratio" value="{$image_ratio}">
  <input type="hidden" name="image_id" value="{$image_id}">
  <fieldset class="cropImage-ratio">
    <legend>{'Aspect Ratio'|@translate}</legend>
    <label><input type="radio" value="1/1" name="ratio"> {'Square'|@translate}</label>
    <label><input type="radio" value="4/5" name="ratio"> 4:5</label>
    <label><input type="radio" value="4/3" name="ratio"> 4:3</label>
    <label><input type="radio" value="5/7" name="ratio"> 5:7</label>
    <label><input type="radio" value="16/9" name="ratio"> 16:9</label>
    <label><input type="radio" value="2.35/1" name="ratio"> 2.35:1</label>
    <label><input type="radio" value="0" checked name="ratio"> {'None'|translate}</label>
    <label><input type="radio" value="original" name="ratio"> {'Original'|translate}</label>
    <label><input type="radio" value="C" name="ratio"> {'Custom'|translate}&nbsp;<input type="text" name="ratioC" id="ratioC" value="" size="5" disabled/> (examples: 2/1 or 1/3)</label>
	</fieldset>
  <input type="submit" name="submit_crop" value="{'Submit'|@translate}">
</fieldset>
</form>
{/if}