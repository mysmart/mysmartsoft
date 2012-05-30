/*
	* Todas las funciones JavaScript que necesitemos para el Admin
	* Autor: Claudio Marrero
	* 22.05.12
*/
jQuery(function(){
	/*
		* Incializa el validation form con estos datos
		* @return
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	//Set en default para la validacion del formulario de ingreso
	jQuery.validator.setDefaults({
    	errorElement: "span",
        errorClass: "my-error-class",
		validClass: "my-valid-class"
    });
	
	/*
		* Usada para devolve un html a partir de una accion, se usa para mostrar los formularios
		* Puede usarse para otros fines, sin necesidad de ser modificada
		* @return = html
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	jQuery('.addNewElement').live('click',function(){
		var $this = jQuery(this);
		var action = $this.attr('action');
		var form = $this.attr('form');
		var $result = jQuery('#responseAjax'+form);
		var data = {
			action: action
		};
		jQuery.post(ajaxurl, data, function(r) {
			if(r){
				$result.html(r).show('slow');
			}
		});
	});
	
	/*
		* Usada para abrir un popup que muestra el formulario para editar datos
		* @return = html
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	jQuery('.editElement').live('click',function(){
		var $this = jQuery(this);
		var action = $this.attr('action');
		var elementId = $this.attr('href');
		var data = {
			action: action,
			elementId:elementId
		};
		jQuery.post(ajaxurl, data, function(r) {
			if(r){
				jQuery.fancybox({
					content:r
				});
			}
		});
		return false;
	});
	/*
		* Usada para eliminar elementos
		* @return = html
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	jQuery('.deleteElement').live('click',function(){
		var $this = jQuery(this);
		var action = $this.attr('action');
		var elementId = $this.attr('href');
		var form = $this.attr('form');
		var $result = jQuery('#responseAjax'+form);
		var dom = jQuery('#ae'+elementId);
		var data = {
			action: action,
			elementId:elementId
		};
		jQuery.post(ajaxurl, data, function(r) {
			if(r){
				if(!r.error){
						dom.remove();
						$result.html(r.content).show('slow');
					}else{
						$result.html(r.content).show('slow');
					}//End error
			}
		},'json');
		return false;
	});
	/*
		* Cancela la carga de datos, usada para ocultar formularios, puede usarse para otros fines
		* @return = html
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	jQuery('#cancelForm-nonce').live('click',function(){
		var $this = jQuery(this);
		var action = $this.attr('action');
		var from = $this.attr('from');
		jQuery('#responseAjax'+from).hide('slow');
		jQuery.fancybox.close();
	});
	
	/*
		* Valida los datos del formulario de cuentas y llama a la funcion para cargar los datos a la BD
		* @return = json
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	jQuery('#formAddAccount').live('submit',function(e){
	   //Ponemos en default el submit
	   e.preventDefault();
	   var email = jQuery('#email').val();

	   //Chequea si el email es correcto, y si todos los campos tienen algo
   		var validator = jQuery(this).validate(
        {
          rules: {
            email: {
				required: true,
				email: true,
				remote: {
                        url: ajaxurl,
                        type: "post",
						data: {
							action:'checkUser',
							email:function(){
								return jQuery('#email').val();
							}
						},
						async: false
                    }
				  
            }//End email
          }
        }// end validator
		).form();
		//Si es correcto pasa esto
		if(validator){
			var data = jQuery(this).serialize();
			jQuery.fancybox.showActivity();
			jQuery.post(ajaxurl, data, function(r) {
				jQuery.fancybox.hideActivity();
				if(r){
					if(!r.error){
						jQuery('#responseAjax1').hide('slow');
						jQuery('#listAccounts').append(r.content);
					}else{
						jQuery('#responseAjax1').html(r.content);
					}//End error
				}//En success
			},"json");//End Post
		}//End validator
	return false;
	});//End formAddAccount
	
	/*
		* Formulario para editar las cuentas, valida y envia los datos
		* @return = json
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	jQuery('#formEditAccount').live('submit',function(e){
	   //Ponemos en default el submit
	   e.preventDefault();
	   var email = jQuery('#email').val();
	   var equalemail = jQuery('#equalemail-nonce').val();
	   var accountId = jQuery('#accountId').val();
	   var dom = jQuery('#ae'+accountId);
	   //Chequea si el email es correcto, y si todos los campos tienen algo
   		var validator = jQuery(this).validate(
        {
          rules: {
            email: {
				required: true,
				email: true,
				remote: {
                        url: ajaxurl,
                        type: "post",
						data: {
							action:'checkUser',
							equalemail:equalemail,
							email:function(){
								return jQuery('#email').val();
							}
						},
						async: false
                    }
				  
            }//End email
          }
        }// end validator
		).form();
		//Si es correcto pasa esto
		if(validator){
			var data = jQuery(this).serialize();
			jQuery.fancybox.showActivity();
			jQuery.post(ajaxurl, data, function(r) {
				jQuery.fancybox.hideActivity();
				if(r){
					if(!r.error){
						jQuery('#responseAjax1').hide('slow');
						dom.html(r.content);
					}else{
						jQuery('#responseAjax1').html(r.content).show('slow');
					}//End error
				}//En success
				jQuery.fancybox.close();
			},"json");//End Post
		}//End validator
	return false;
	});//End formAddAccount
	
	/*
		* Valida los datos del formulario de planes y llama a la funcion para cargar los datos a la BD
		* @return = json
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	jQuery('#formAddPlan').live('submit',function(e){
	   //Ponemos en default el submit
	   e.preventDefault();
	   //Chequea si el email es correcto, y si todos los campos tienen algo
   		var validator = jQuery(this).validate(
        {
          rules: {
            plan: {
				required: true
            }//End plan
          }
        }// end validator
		).form();
		//Si es correcto pasa esto
		if(validator){
			var data = jQuery(this).serialize();
			jQuery.fancybox.showActivity();
			jQuery.post(ajaxurl, data, function(r) {
				jQuery.fancybox.hideActivity();
				if(r){
					if(!r.error){
						jQuery('#responseAjax').hide('slow');
						jQuery('#listPlans').append(r.content);
					}else{
						jQuery('#responseAjax').html(r.content);
					}//End error
				}//En success
			},"json");//End Post
		}//End validator
	return false;
	});//End submit
	
	/*
		* Valida los datos del formulario de modulos y llama a la funcion para cargar los datos a la BD
		* @return = json
		* Autor: Claudio Marrero
		* 22.05.12
	*/
	jQuery('#formAddModule').live('submit',function(e){
	   //Ponemos en default el submit
	   e.preventDefault();
	   //Chequea si el email es correcto, y si todos los campos tienen algo
   		var validator = jQuery(this).validate(
        {
          rules: {
            module: {
				required: true
            }//End plan
          }
        }// end validator
		).form();
		//Si es correcto pasa esto
		if(validator){
			var data = jQuery(this).serialize();
			jQuery.fancybox.showActivity();
			jQuery.post(ajaxurl, data, function(r) {
				jQuery.fancybox.hideActivity();
				if(r){
					if(!r.error){
						jQuery('#responseAjax2').hide('slow');
						jQuery('#listModules').append(r.content);
					}else{
						jQuery('#responseAjax2').html(r.content);
					}//End error
				}//En success
			},"json");//End Post
		}//End validator
	return false;
	});//End submit
	
});
