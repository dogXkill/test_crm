
                function search_fio() {
                        var query = '';
                        var surname = $('#search-surname').val();
                        if (surname !== null && surname !== '') {
                                query = query + '&surname=' + surname;
                        }
                        var name = $('#search-name').val();
                        if (name !== null && name !== '') {
                                query = query + '&name=' + name;
                        }

                        query = encodeURI('/acc/users/users.php?search_text=1' + query + '' + archive_link);
                        location = query;
                }
                function search_id() {
                        var user_id = Number($('#search-id').val());
                        if (isNaN(user_id)) {
                                alert('Номер сотрудника должен быть числом!');
                        } else {

                                if (user_id !== null && user_id !== '') {

                                        if (user_id == 0) {
                                                query = '/acc/users/users.php';
                                        } else {
                                                query = '/acc/users/users.php?search_id=' + user_id;
                                        }

                                }
                                location = query;
                        }
                }


function check(){



        var obj = document.editus;

        if(obj.surname.value==""){
                alert("Введите Фамилию");
                obj.surname.focus();
                return false;
        }
        if(obj.name.value==""){
                alert("Введите Имя");
                obj.name.focus();
                return false;
        }
        if(obj.father.value==""){
                alert("Введите Отчество");
                obj.father.focus();
                return false;
        }
        if(obj.doljnost.value=="0"){
                alert("Выберите должность");
                obj.doljnost.focus();
                return false;
        }

                var department = $('#user-department-select').val();
        if (department == 'нет') {
                alert("Необходимо указать отдел");
                                $('#user-department-select').focus();
                return false;
        }

        if(obj.work_time.value=="0" || obj.work_time.value==""){
                alert("Рабочие часы не введены. По умолчанию оставляем 9?");
                $("#work_time").val(9);
                obj.work_time.focus();
                return false;
        }




        if( !validateEmail(obj.email.value) && obj.email.value!=="") {
            alert("Введите правильный емейл!");
            obj.email.focus();
            return false;
        }


         mobile = $('#mobile').val();
        if (mobile.length !== 12) {
            alert('Заполните корректно Номер телефона');
            $('#mobile').focus();
            return false;
        }


        var doljnost = $('#doljnost').val();
        if (doljnost == 44) {
                dep = document.getElementById('user-department-select');
                depId = dep['selectedOptions'][0]['attributes']['data-dep'].value;
                if (depId == 22 || depId == 23) {
                } else {
                        alert('Сотрудник с должностью "Бригадир участка" может быть привязан только к одному из двух отделов: "Сборочный участок Ростов" или "Сборочный участок Тверь"');
                        return false;
                }
        }


        if(obj.login.value !== "" && $('#pass').val() == ""){
                        alert("Необходимо задать пароль");
                        $('#pass').focus();
                        return false;
        }

                if(obj.pass.value !== obj.repass.value){
                        alert("Пароль и подтверждение не совпадают");
                        $('#repass').focus();
                        return false;
        }


        if($('#login_err').val() == 1){
        alert('Пользователь с таким логином уже существует');
        $('#login').focus();
        return false;
        }

        if($('#email_err').val() == 1){
        alert('Пользователь с таким email уже существует');
        $('#email').focus();
        return false;
        }


 }

function check_new_login(){
    var login = $('#login').val();


                $.ajax({
                url: 'backend/check_new_login.php',
                method: 'GET',
                data: {login: login},
                dataType: 'html',
                async: false,
                success: function(data){
                        data = Number(data);
                        if (data == 1) {
                                alert('Пользователь с таким логином уже существует');
                                $('#login').focus();
                                $('#login_err').val("1");
                                } else if (data == 0) {
                                $('#login_err').val("0");
                        }
                }
                });
    }



function check_new_email(){

                var email = $('#email').val();
                if (email !== '') {
                        $.ajax({
                                url: 'backend/check_new_email.php',
                                method: 'GET',
                                data: {email: email},
                                dataType: 'html',
                                async: false,
                                success: function(data){
                                        data = Number(data);
                                        if (data == 1) {
                                                alert('Пользователь с таким email уже существует');
                                                $('#email').focus();
                                                $('#email_err').val("1");
                                                return false;
                                        } else if (data == 0)  {
                                                $('#email_err').val("0");
                                        }
                                }
                        });


                }else{$('#email_err').val("0"); }
            }





function validateEmail($email) {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        return emailReg.test( $email );
}



function arch_user(id, act) {
                if(act == "del_archive"){msg = "Удалить пользователя в архив?";}
                if(act == "restore"){msg = "Восстановить пользователя из архива?";}
                if(act == "del_final"){msg = "Удалить пользователя окончательно и навсегда?";}
        if(confirm(msg))
                document.location = 'users.php&archive=' +act+ '&del=' + id;
        }

 function table_access_checkbox(id) {
        var checked = document.getElementById(id).checked;
        if (id == 'table_access_yes') {
                if (checked == true) {
                        $('#table_access_no').prop('checked', false);
                } else {
                        $('#table_access_yes').prop('checked', true);
                }
                $('#table_access_select_group').attr('class', '');
                $('#table_access_select_dep').attr('class', '');
        } else {
                if (checked == true) {
                        $('#table_access_yes').prop('checked', false);
                } else {
                        $('#table_access_no').prop('checked', true);
                }
                $('#table_access_select_group').attr('class', 'none');
                $('#table_access_select_dep').attr('class', 'none');
        }
}



            	$('#order_access_yes').on('click', function() {
                        $('#order_access_type_tr').toggleClass('none');
                        $('#order_access_edit_tr').toggleClass('none');
                        $('#order_access_payment_tr').toggleClass('none');
                });
                $('#order_access_no').on('click', function() {
                        $('#order_access_type_tr').toggleClass('none');
                        $('#order_access_edit_tr').toggleClass('none');
                        $('#order_access_payment_tr').toggleClass('none');
                });

                $('#proizv_access_yes').on('click', function() {
                        $('#proizv_access_type_tr').toggleClass('none');
                        $('#proizv_access_edit_tr').toggleClass('none');
                });
                $('#proizv_access_no').on('click', function() {
                        $('#proizv_access_type_tr').toggleClass('none');
                        $('#proizv_access_edit_tr').toggleClass('none');
                });




            $('#order_access_yes').on('click', function() {
                            $('#order_access_type_tr').toggleClass('none');
                            $('#order_access_edit_tr').toggleClass('none');
                            $('#order_access_payment_tr').toggleClass('none');
                    });
                    $('#order_access_no').on('click', function() {
                            $('#order_access_type_tr').toggleClass('none');
                            $('#order_access_edit_tr').toggleClass('none');
                            $('#order_access_payment_tr').toggleClass('none');
                    });

                    $('#proizv_access_yes').on('click', function() {
                            $('#proizv_access_type_tr').toggleClass('none');
                            $('#proizv_access_edit_tr').toggleClass('none');
                    });
                    $('#proizv_access_no').on('click', function() {
                            $('#proizv_access_type_tr').toggleClass('none');
                            $('#proizv_access_edit_tr').toggleClass('none');
                    });


                                                                                        var customOptions = {
                        onKeyPress: function (val, e, field, options) {

                                        if (val.replace(/\D/g, '').length === 2) {

                                                        val = val.replace('8', '');
                                                        field.val(val);
                                        }
                                        field.mask("+79999999999", options);
                        },
                        placeholder: "+7__________"
        };

        $("#mobile").mask("+79999999999", customOptions);


function select_all_opt(id) {
        elems = document.getElementById(id).children;
                for (i = 0; i <= elems.length; i++) {
                        if (elems[i] !== undefined) {
                                if (elems[i].attributes['value'].value != 0 && Number(elems[i].attributes['value'].value) != 0) {
                                        elems[i].selected = 'selected';
                                }
                        }
                }
}

function delUser() {
        var elem = event.target;
        uid = elem.attributes['data-id'].value;
        switch (elem.name) {
                case 'archive':
                $.ajax({
                        url: 'backend/delete_handler.php',
                        method: 'GET',
                        data: {oper: "archive", uid: uid},
                        dataType: 'html',
                        async: false,
                        success: function(data){
                                $('#user_' + uid).remove();
                                alert('Сотрудник перемещен в архив');
                        }
                });
                        break;

                case 'restore':
                $.ajax({
                        url: 'backend/delete_handler.php',
                        method: 'GET',
                        data: {oper: "restore", uid: uid},
                        dataType: 'html',
                        async: false,
                        success: function(data){
                                $('#user_' + uid).remove();
                                alert('Сотрудник восстановлен из архива');
                        }
                });
                        break;

                case 'delFinal':
                        $.ajax({
                                url: 'backend/delete_handler.php',
                                method: 'GET',
                                data: {oper: "delFinal", uid: uid},
                                dataType: 'html',
                                async: false,
                                success: function(data){
                                        $('#user_' + uid).remove();
                                        alert('Сотрудник удален окончательно');
                                }
                        });
                        break;
        }
}


function doubleCheckboxes(id) {
        var checked = document.getElementById(id).checked;
        var name = document.getElementById(id).name;

        if (name == 'order_access_type' || name == 'proizv_access_edit' || name == 'order_access_payment' || name == 'proizv_access_type' || name == 'order_access_edit' || name == 'statistics_access') {
                        for (var i = 0; i <= 2; i++) {

                                if (id == name + '_' + i) {

                                        $('#' + name + '_' + i).prop('checked', true);
                                } else {
                                        $('#' + name + '_' + i).prop('checked', false);
                                }
                        }
        } else {
        if (id == name + '_yes') {
 	 if (checked == true) {
 		 $('#' + name + '_no').prop('checked', false);
 	 } else {
 		 $('#' + name + '_yes').prop('checked', true);
 	 }
        } else {
 	 if (checked == true) {
 		 $('#' + name + '_yes').prop('checked', false);
 	 } else {
 		 $('#' + name + '_no').prop('checked', true);
 	 }
        }
        }
}

