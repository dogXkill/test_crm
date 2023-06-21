
class EditShipment extends React.Component {
  constructor(props) {
    super(props);
    var id = document.getElementById('editScript').attributes[1].value;
    this.state = {
      isLoaded: false,
      apiData: '',
      error: null,
      item: '',
      itemApplications: [],
      applicationsList: [],
      jobNames: [],
      lastAddedJob: 0,
      buttonSaved: false,                    // Флаг активности кнопки сохранения
      jobAdded: false,
      appAdded: false,
      saveError: false,                      // Флаг ошибки сохранения
      saveErrorMessage: ''                   // Текст ошибки сохранения
    }
    this.editShipping = this.editShipping.bind(this);
    this.saveShipping = this.saveShipping.bind(this);
  }

  componentDidMount() {
  fetch("data.php")
    .then(res => res.json())
    .then(
      (result) => {
        var id = document.getElementById('editScript').attributes[1].value;
        var applicationsList = result.applicationsList[0];
        var jobNames = result.jobNames[0];
        var item = '';
        var itemApplications = '';
        result.shipmentsList[0].forEach((elem) => {
          var id = document.getElementById('editScript').attributes[1].value;
          if (elem['id'] == id) {
            item = elem;
            itemApplications = elem.applications;
            if (id == 0) {
              item.applications = [];
            }
          }

        });

        this.setState({
          isLoaded: true,
          apiData: result,
          item: item,
          itemApplications: itemApplications,
          applicationsList: applicationsList,
          jobNames: jobNames
        });
        console.log(this.state.apiData);
        console.log(this.state.item);

      },
      (error) => {
        this.setState({
          isLoaded: true,
          error
        });
      }
    )
}



  editShipping(event) {
    var target = event.target;

    switch (event.target.name) {

      case 'appChange':   // Изменение номера заявки
        var appId = event.target.attributes['data-app'].value;
        var num = this.state.item.applications.indexOf(appId);
        this.state.item.applications.map((app, i) => (
          this.state.item.applications[i] == appId ? this.state.item.applications[i] = event.target.value : ''
        ));
        this.state.item.jobs.jobsList.map((job, i) => (
          job.applicationId == appId ? job.applicationId = event.target.value : ''
        ));
        this.state.item.tiraz.map((elem, i) => (
          elem.tirazAppId == appId
          ? this.state.item.tiraz[i] = {"tirazAppId": event.target.value, "tirazQuantity": 0, "countError": ""}
          : ''
        ))
        break;

      case 'dateChange':    // Изменение даты
        this.state.item.shippingDate = event.target.value;
        break;

      case 'statusChange':  // Изменение статуса
        this.state.item.status = event.target.value;
        this.state.apiData.shipmentStatuses[0].map((s) => (
          s.id == event.target.value ? this.state.item.statusName = s.name : ''
        ))
        break;
      case 'jobChange':  // Изменение работы в заявке
        var jobValue = event.target.value;
        var appId = event.target.attributes['data-app'].value;
        var jobId = event.target.attributes['data-job'].value;
        this.state.item.jobs.jobsList.map((job) => (
          job.applicationId == appId ? job.jobsList[0][(job.jobsList[0].indexOf(jobId))] = jobValue : ''
        ));
        console.log(this.state.item);
        break;

      case 'addJobs':     // Добавление новых работ
        var appId = event.target.attributes['data-app'].value;
         this.state.item.jobs.jobsList.map((job, i) => (
          job.applicationId == appId
          ? (this.state.apiData.jobNames[0].map((elem) => (
            this.state.jobAdded == false
            ? elem.id && job.jobsList[0].indexOf(elem.id) < 0
              ? (job.jobsList[0].push(elem.id),
                this.state.jobAdded = true)
              : ''
            : ''
            )),
            this.state.jobAdded = false)
          : '',
          job.applicationId == appId
          ? this.state.lastAddedJob += 1
          : ''
        ));
        console.log(this.state.item);
        break;

      case 'addAplication':     // Добавление новой заявки

        this.state.item.applications.push("0");
        this.state.item.jobs.jobsList.push({"applicationId": "0", "jobsList": { "0": ["23", "4", "20", "11", "22"]} });
        this.state.item.tiraz.push({"tirazAppId": "0", "tirazQuantity": 0, "countError": 0});
        break;

      case 'deleteJob':         // Удаление работы
        var appId = event.target.attributes['data-app'].value;
        let jobId = event.target.attributes['data-job'].value;
        let newList = [];
        this.state.item.jobs.jobsList.map((j) => (
          j.applicationId == appId
          ? j.jobsList[0].map((jobListId) => (
            jobListId !== jobId ? newList.push(jobListId) : '' ))
          : '',
          j.applicationId == appId ? j.jobsList[0] = newList : ''
        ))
        break;

      case 'divisionChange':  // Изменение подразделения
        this.state.item.division = event.target.value;
        this.state.apiData.divisionsList[0].map((d) => (
          d.id == event.target.value ? this.state.item.divisionName = d.name : ''
        ))
        break;

      case 'tirazChange':   // Изменение тиража в заявке
        var appId = event.target.attributes['data-app'].value;
        var tiraz = Number(event.target.value);
        console.log(tiraz);
        this.state.applicationsList.map((app) => (app.numOrd == appId ? this.state.item.tiraz.map((elem) => (elem.tirazAppId == appId ? elem.countError = '' : '')) : ''))
        this.state.applicationsList.map((app) => (
          app.numOrd == appId
          ? tiraz <= app.tiraz
            ? tiraz <= (app.tiraz - app.packed)
              ? this.state.item.tiraz.map((elem) => (elem.tirazAppId == appId ? elem.tirazQuantity = tiraz : ''))
              : this.state.item.tiraz.map((elem) => (elem.tirazAppId == appId ? elem.countError = 'Количество к отгрузке не может быть больше остатка от упакованных изделий (тираж ' + app.tiraz + ', упакованно ' + app.packed + ')' : ''))
            : this.state.item.tiraz.map((elem) => (elem.tirazAppId == appId ? elem.countError = 'Количество к отгрузке не может быть больше тиража в заявке (' + app.tiraz + ')' : ''))
          : ''
        ));
        break;

      case 'deleteApp':     // Удалить заявку из списка
        var appId = event.target.attributes['data-app'].value;
        console.log(appId);
        var itemApps = [];
        this.state.itemApplications.map((app, i) => (this.state.itemApplications[i] !== appId ? itemApps.push(app) : '' ));
        this.state.itemApplications = itemApps;
        var itemApps = [];
        this.state.item.applications.map((app, i) => (this.state.item.applications[i] !== appId ? itemApps.push(app) : '' ));
        this.state.item.applications = itemApps;
        var itemJobs = [];
        this.state.item.jobs.jobsList.map((job, i) => (job.applicationId !== appId ? itemJobs.push({"applicationId": job.applicationId, "jobsList": { "0": job.jobsList[0]} }) : '' ));
        this.state.item.jobs.jobsList = itemJobs;
        var itemTiraz = [];
        this.state.item.tiraz.map((tir, i) => (tir.tirazAppId !== appId ? itemTiraz.push(tir) : '' ));
        this.state.item.tiraz = itemTiraz;
        break;
    }
    this.state.buttonSaved = false;
    console.log(this.state.item);
    this.setState({});

  }

  saveShipping() {

    var id = this.state.item.id;
    var applications = [];
    var jobs = [];
    var tiraz = [];
    var shippingDate = this.state.item.shippingDate;
    var division = this.state.item.division;
    var status = this.state.item.status;
    var create = this.state.item.id == 0 ? 'Y' : 'N';
    this.state.saveError = false;
    this.state.saveErrorMessage = '';

    this.state.item.tiraz.forEach((item, i) => {
      if (item.tirazAppId !== '' && item.tirazAppId !== 0) {
        tiraz.push(item.tirazAppId + '_' + item.tirazQuantity);
        if (item.tirazQuantity == 0) {
          this.state.saveError = true;
          this.state.saveErrorMessage = 'Количество изделий не может быть равно 0 (заявка ' + item.tirazAppId + ')';
          console.log(item.tirazQuantity);
        }
      }
    });
    tiraz = tiraz.join('slsh');

    this.state.item.jobs.jobsList.forEach((item, i) => {
      if (item.applicationId !== '') {
        jobs.push(item.applicationId + '_' + item.jobsList[0].join('-'));
      }
    });

    this.state.item.applications.forEach((item, i) => {
      if (item !== '') {
        if (item !== 0) {
          applications.push(item);
        } else {
          this.state.saveError = true;
          this.state.saveErrorMessage = 'Выберите номер заявки из списка или удалите поле с пустой заявкой';
        }
      }
    });

    if (this.state.item.applications == '') {
      this.state.saveError = true;
      this.state.saveErrorMessage = 'Добавьте хотя бы одну заявку';
    }


    applications = applications.join('slsh');

    jobs = jobs.join('slsh');

    if (this.state.saveError == true) {
      alert(this.state.saveErrorMessage);
    } else {
      var url = 'save.php?id=' + id + '&applications=' + applications + '&jobs=' + jobs + '&tiraz=' + tiraz + '&shippingDate=' + shippingDate + '&division=' + division + '&status=' + status + '&create=' + create;
      console.log(url);
      fetch(url).then(res => res.text()).then((result) => {window.location.href = '/acc/applications/sendings/';})
      this.state.buttonSaved = true;
    }

    this.setState({});


  }

  render() {
    const { error, isLoaded, apiData } = this.state;
    if (error) {
      return <div>Error: {error.message}</div>;
    } else if (!isLoaded) {
      return <div className="edit_cont">

                <div className="edit_cont_body">
                  <a href="index.php" className="shipments_menu_link">К списку отправок</a>
                  <br />
                  <div id="preloader"><img src="../../../i/load2.gif"/></div>
                </div>
              </div>;
    } else {
      return(
        <div className="edit_cont">
          <div className="edit_cont_body">
            <a href="index.php" className="shipments_menu_link">К списку отправок</a>
            <br />
            <div className="flex_head_fields">
            <div className="edit_text">Номер отправки:</div>
            <input className="shipment_filter_number opacity" type="text" value={this.state.item.id !== '0' ? this.state.item.id : this.state.apiData.newId} readOnly />
            <div className="edit_text">Дата отправки:</div>
            <input className="shipment_filter_date" type="date" value={this.state.item.shippingDate} name="dateChange" onChange={this.editShipping}  />
            <div  className="edit_text">Подразделение:</div>
            <select name="divisionChange" className="filter shipments_filter_select" value={this.state.item.division} onChange={this.editShipping}>
              {
                this.state.apiData.divisionsList[0].map((division) => (
                  <option value={division.id} id={"division_" + division.id } key={division.id}>{division.name}</option>
                 ))
              }
            </select>
            <div className="edit_text">Статус:</div>
            <select value={this.state.item.status} name="statusChange" className="filter shipments_filter_select" onChange={this.editShipping}>
              {
                this.state.apiData.shipmentStatuses[0].map((status) => (
                  <option value={status.id} id={"status_" + status.id } key={status.id}>{status.name}</option>
                 ))
              }
            </select>
            </div>
            <div className="edit_text">Список заявок:</div>
            <br />
            <div className="applications_list">
            { this.state.item.applications.map((elem, i)=>(
              elem == 0
              ? <div className="application_table new_app_border">
                    <div className="new_app_column">
                      <span className="app_table_heading">Номер заявки</span>
                      <select name="appChange" data-app="0" className="add_application application shipments_filter_select" onChange={this.editShipping}>
                        <option value="0">Выберите из списка...</option>
                        { this.state.applicationsList.map((appInList) => ( <option key={'app_list_' + appInList.numOrd} value={appInList.numOrd} disabled={this.state.item.applications.indexOf(appInList.numOrd) >=0 ? 'disabled' : null}>{appInList.numOrd}</option>))}
                      </select>
                    </div>
                    <div className="new_app_column"></div><div className="new_app_column"></div>
                    <div className="new_app_column flex_center">
                      <button name="deleteApp" data-app={elem} className="delete_app" onClick={this.editShipping}>Удалить из списка</button>
                    </div>
                </div>

              : this.state.applicationsList.map((app, j) => (
                elem == app.numOrd
                ? <div className="application_table" key={'app_' + app.numOrd}>
                    <div className="application_table_column">
                      <span className="app_table_heading">Номер заявки</span>
                      <select name="appChange" data-app={app.numOrd} className="select_application application shipments_filter_select" value={app.numOrd} onChange={this.editShipping}>
                      { this.state.applicationsList.map((appInList) => ( <option key={'app_list_' + appInList.numOrd} id={'app_' + app.numOrd + '_choose_' + appInList.numOrd} value={appInList.numOrd} disabled={this.state.item.applications.indexOf(appInList.numOrd) >=0 ? 'disabled' : null}>{appInList.numOrd}</option>))}
                      </select>
                      <div className="app_descr_info"><a href={"/acc/applications/edit.php?uid=" + app.id} target="_blank">Заявка {app.numOrd}</a> {app.descriptionText}</div>
                    </div>
                    <div className="application_table_column">
                      <span className="app_table_heading">Количество изделий</span>
                      {this.state.item.tiraz.map((elem, k) => (elem.tirazAppId == app.numOrd
                        ? <div key={'num-' + k}>
                            <input key={k} type="number" className="quantityNumber application shipment_number_input" type="number" name="tirazChange" onChange={this.editShipping}  data-app={app.numOrd} value={elem.tirazQuantity} />
                            <div key={'q-err-' + k} className="quantity_err">{elem.countError == '' || elem.countError == 0 ? '' : elem.countError}</div>
                          </div>
                        : ''
                      ))}
                    </div>
                    <div className="application_table_column">
                      <span className="app_table_heading">Список работ</span>
                      {
                         this.state.item.jobs.jobsList.map((job) => (
                           job.applicationId == app.numOrd
                           ? job.jobsList[0].map((currentJob, i) => (
                             <div key={'job_cont_' + i + '_app_' + app.numOrd} className="select_flex_cont">
                                <select name="jobChange" data-app={app.numOrd} data-job={currentJob} key={'chooseApp_' + job.applicationId + '_job_' + i} value={currentJob} className="select_job application shipments_filter_select" onChange={this.editShipping}>
                                {this.state.apiData.jobNames[0].map((jobList, u) => (<option value={jobList.id} key={u} id={'job_' + jobList.id} disabled={jobList.disabled || job.jobsList[0].indexOf(jobList.id) >= 0 ? 'disabled' : null}>{jobList.name}</option>)  )}
                                </select>
                                <img name="deleteJob" key={'job_del_' + i + '_app_' + app.numOrd} data-app={app.numOrd} data-job={currentJob} className="delete_img" src="/acc/i/del.gif" onClick={this.editShipping} />
                              </div>
                            ))
                           : ''
                         )) }
                      <button name="addJobs" data-app={app.numOrd} className="application shipment_filter_button" onClick={this.editShipping}>Добавить работы</button>
                    </div>
                    <div className="application_table_column flex_center">
                      <button name="deleteApp" data-app={app.numOrd} className="delete_app" onClick={this.editShipping}>Удалить из списка</button>
                    </div>
                  </div>

                : ''
             ))
           )) }

            </div>
            <button name="addAplication" className="application shipment_filter_button" onClick={this.editShipping}>Добавить заявку</button>
            <div className="oper_btns">
              <button className={this.state.buttonSaved == true ? 'save_btn saved' : 'save_btn'} name="saveShipping" onClick={this.saveShipping}>{this.state.buttonSaved == true ? 'Сохранено' : 'Сохранить'}</button>
              <div className="flex_center save_link"><a className="" href="index.php">Назад к списку</a></div>
            </div>
          </div>

        </div>
    );
  }
  }

}

ReactDOM.render(
  <EditShipment />,
  document.getElementById('app')
);
