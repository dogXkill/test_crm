
class Shipments extends React.Component {

  constructor(props) {
    super(props);
    this.state = {
      isDivision: 0,                         // Работает ли в обособленном подразделении
      divisionId: 0,                         // ID обособленного подразделения сотрудника
      apiData: '',                           // Data
      isLoaded: false,                       // Флаг загрузки данных
      error: null,                           // Ошибки
      allShipments: [],                      // Все отправки
      allShipmentsNum: [],                   // ID всех отправок
      showAll: true,                         // Флаг - показывать все
      shipmentsToShow: [],                   // Отгуркзи к показу
      shipmentsToShowNum: [],                // ID отправок к показу
      filterNumber: '',                      // Фильтр по номеру
      filterDivision: 0,                     // Фильтр по подразделению
      filterStatus: 0,                       // Фильтр по статусу
      filterNumberElements: '',              // Фильтр по номеру - элементы
      filterDivisionElements: '',            // Фильтр по подразлелению - элементы
      filterStatusElements: '',              // Фильтр по статусу - элементы
      showPopup: false,                      // Попап с заявками
      popupShipmentId: 0,                    // ID отправки в попапе
      allowedToEdit: 0,                      // Доступ к редактированию отправок
      showArchive: false,                    // Флаг показа архива

    }
    this.filterShow = this.filterShow.bind(this);
    this.showPopup = this.showPopup.bind(this);
  }

  componentDidMount() {
  fetch("data.php")
    .then(res => res.json())
    .then(
      (result) => {

        var nums = [];
        var toShow = [];
        result.shipmentsList[0].forEach((item) => {
          nums.push(item['id']);
          if (item.archive == 0) {toShow.push(item);}
        });
        var elem = document.getElementById('indexScript');
        var allowed = elem.attributes['data-allowed'].value;
        var isDivision = elem.attributes['data-isDivision'].value;
        var divisionId = isDivision == 1 ? elem.attributes['data-division'].value : 0;
        this.setState({
          isLoaded: true,
          apiData: result,
          allShipments: result.shipmentsList[0],
          allShipmentsNum: nums,
          shipmentsToShow: toShow,
          shipmentsToShowNum: nums,
          allowedToEdit: allowed,
          isDivision: isDivision,
          divisionId: divisionId
        });
      },
      (error) => {
        this.setState({
          isLoaded: true,
          error
        });
      }
    )
}

   filterShow(event) {
    var target = event.target;

    switch (target.tagName) {
      case "INPUT":
        var uniqueId = (target.value) ? target.value : '0';
        break;
      case "SELECT":
        var filterId = target.selectedOptions[0].id;
        var uniqueId = filterId.split('_')[1];
        break;
    }

    switch (target.attributes.name.value) {
      case "showArchive":
        this.state.showArchive = this.state.showArchive == false ? true : false;
        break;
      case "divisionFilter":
        this.state.filterDivision = uniqueId;
        break;
      case "statusFilter":
        this.state.filterStatus = uniqueId;
        break;
      case "numberFilter":
        this.state.filterNumber = (uniqueId == 0) ? '' : uniqueId;
        break;
      case "clearFilters":
        this.state.filterNumber = '';
        this.state.filterDivision = 0;
        this.state.filterStatus = 0;
        break;
      case 'deleteShipment':
        var check = confirm('Вы действительно хотите удалить отправку?');
        if (check == true) {
          var shipId = event.target.attributes['data-app'].value;
          fetch('delete.php?id=' + shipId)
          .then(res => res.text())
          .then((result) => {
            var showList = [];
            var showListNums = [];
            var allList = [];
            var allListNums = [];
            this.state.apiData.shipmentsList[0].map((ship)=>(
              ship.id !== shipId
              ? (this.state.shipmentsToShowNum.indexOf(ship.id) >= 0 ? (showList.push(ship), showListNums.push(ship.id)) : '',
                allList.push(ship),
                allListNums.push(ship.id) )
              : ''
            ));
            this.state.apiData.shipmentsList[0] = showList;
            this.state.shipmentsToShow = showList;
            this.state.shipmentsToShowNum = showListNums;
            this.state.allShipments = allList;
            this.state.allShipmentsNum = allListNums;
            this.setState({});
          })
        }


        break;
      case 'inArchive':
        var check = confirm('Вы действительно хотите переместить отправку в архив?');
        if (check == true) {
          var shipId = event.target.attributes['data-app'].value;
          this.state.allShipments.map((item, i) => {
            item.id == shipId ? item.archive = "1" : null
          });
          this.setState({});
          fetch('update.php?id=' + shipId + '&oper=inarchive').then(res => res.text()).then((result) => {})
        }
        break;
      case 'fromArchive':
        var check = confirm('Восстановить отправку из архива?');
        if (check == true) {
          var shipId = event.target.attributes['data-app'].value;
          this.state.allShipments.map((item, i) => {
            item.id == shipId ? item.archive = "0" : null
          });
          fetch('update.php?id=' + shipId + '&oper=fromarchive').then(res => res.text()).then((result) => {})
        }
        break;
      case 'statusChange':
        var shipId = target.attributes['data-ship'].value;
        this.state.apiData.shipmentsList[0].map((elem)=>(
          elem.id == shipId
          ? (elem.status = target.value,
            this.state.apiData.shipmentStatuses[0].map((stat)=>(
              stat.id == target.value ? elem.statusName = stat.name : ''
              ))
            )
          : ''
        ));
        fetch('update.php?oper=status&id=' + shipId + '&status=' + target.value).then(res => res.text()).then((result) => {})
        alert('Статус отправки изменен');
        break;
    }

    this.state.showAll = (this.state.filterNumber == 0 && this.state.filterDivision == 0 && this.state.filterStatus == 0 && this.state.showArchive == false) ? true : false;

//    if (this.state.showAll == false) {
      this.state.shipmentsToShowNum = [];
      this.state.shipmentsToShow = [];

      this.state.allShipments.forEach((item) => {
        var inFilterNumber = false;
        var inFilterDivision = false;
        var inFilterStatus = false;
        var inFilterArchive = false;
        if (this.state.filterNumber != 0) {
          if (item['id'] == this.state.filterNumber) {inFilterNumber = true;}
        } else {
          inFilterNumber = true;
        }
        if (this.state.filterDivision != 0) {
          if (item['division'] == this.state.filterDivision) {inFilterDivision = true;}
        } else {
          inFilterDivision = true;
        }
        if (this.state.filterStatus != 0) {
          if (item['status'] == this.state.filterStatus) {inFilterStatus = true;}
        } else {
          inFilterStatus = true;
        }

        if (this.state.showArchive == true) {
          if (item['archive'] == "1") {inFilterArchive = true;}
        } else {
          if (item['archive'] == "0") {inFilterArchive = true;}
          if (item['id'] == 29) {console.log(inFilterArchive);}
        }

        if (inFilterNumber == true && inFilterDivision == true && inFilterStatus == true && inFilterArchive == true) { this.state.shipmentsToShowNum.push(item['id']); }

      });

      this.state.shipmentsToShowNum.forEach((item) => {
        this.state.allShipments.forEach((shipment) => {
          if (shipment['id'] == item) {this.state.shipmentsToShow.push(shipment);}
        });

      });


    this.setState({});
  }

  showPopup(event) {
    if (event.target.attributes.name)
    {
      switch (event.target.attributes.name.value)
      {
        case 'openPopup':
          this.state.showPopup = true;
          this.state.popupShipmentId = event.target.attributes[0].value;
          break;
        case 'closePopup':
          this.state.showPopup = false;
          break;
      }
    }

    this.setState({});
  }
	//<a className="shipments_menu_link" href="edit.php?id=0">Добавить отправку</a>
	//<img src="../../../i/load2.gif"/>
  render() {
    const { error, isLoaded, apiData } = this.state;
    if (error) {
      return <div>Error: {error.message}</div>;
    } else if (!isLoaded) {
      return   <div>
                <div className="shipments_menu_flex">
                  <div className="shipments_menu_links">
                    <a className="shipments_menu_link" href="edit.php?id=0"><i className="fa-solid fa-square-plus icon_btn_r21 icon_btn_blue"></i> Добавить отправку</a>
                  </div>
                </div>
                
				
              </div>;
    } else {
      return(
      <div>
        {
          this.state.showPopup == true
          ? <div name="closePopup" className="apps_popup_cont" onClick={this.showPopup}>
              <div className="apps_popup">
                <div className="popup_head">
                  <div>Отправка № {this.state.popupShipmentId}</div>
                  <img className="delete_img" src="/acc/i/del.gif" name="closePopup" onClick={this.showPopup} />
                </div>
                <table className="table_list">
                  <tbody>
                    <tr className="table_tr">
                      <td className="table_heading center table_td">Заявка №</td>
                      <td className="table_heading center table_td">Тип изделия</td>
                      <td className="table_heading center table_td">Размер изделия</td>
                      <td className="table_heading center table_td">Название заказчика / Артикул</td>
                      <td className="table_heading center table_td">Общий тираж</td>
                      <td className="table_heading center table_td">Количество в отправке</td>
                      <td className="table_heading center table_td">Статус сборки</td>
                      <td className="table_heading center table_td">Статус упаковки</td>
                      <td className="table_heading center table_td">Наклейка</td>
                    </tr>

                      {
                        this.state.apiData.shipmentsList[0].map((ship, i)=>(
                          ship.id == this.state.popupShipmentId
                          ? ship.applications.map((elem) => (
                            this.state.apiData.applicationsList[0].map((app, j)=>(
                              app.numOrd == elem
                              ? <tr key={j} className="table_tr">
                                  <td className="center table_td">{app.numOrd}</td>
                                  <td className="center table_td">{app.izdTypeName}</td>
                                  <td className="center table_td">{app.izdW}x{app.izdV}x{app.izdB}</td>
                                  <td className="center table_td">{app.clientName}</td>
                                  <td className="center table_td">{app.tiraz}</td>
                                  { ship.tiraz.map((tir, k)=>( tir.tirazAppId == app.numOrd ? <td id="popup_tiraz" key={k} className="center table_td">{tir.tirazQuantity}</td> : null)) }

                                  <td className="flex_center table_td">
                                    {ship.sobranoQuantity.map((elem)=>(elem.numOrd == app.numOrd
                                      ?  <div>
                                          <div className="workout_green_div" style={{width: elem.percent}}></div>
                                          <div className="workout_outer_div">
                                            {elem.quantity} из { ship.tiraz.map((tir, k)=>( tir.tirazAppId == app.numOrd ? tir.tirazQuantity : null)) }
                                            <br />
                                            <span className="workout_perc_text">{elem.percent} %</span>
                                          </div>

                                        </div>
                                      : null ))}


                                  </td>
                                  <td className="flex_center table_td">
                                    {ship.packedQuantity.map((elem)=>(elem.numOrd == app.numOrd
                                      ?  <div>
                                          <div className="workout_green_div" style={{width: elem.percent}}></div>
                                          <div className="workout_outer_div">
                                            {elem.quantity} из {ship.tiraz.map((tir, k)=>( tir.tirazAppId == app.numOrd ? tir.tirazQuantity : null))}
                                            <br />
                                            <span className="workout_perc_text">{elem.percent} %</span>
                                          </div>
                                        </div>
                                      : null ))}
                                  </td>
                                  <td className="center table_td"><a href={"../nakl/?num_ord=" + app.numOrd + "&qty=9"} target="_blank">Наклейка</a></td>
                                </tr>
                              : null
                            ))
                          ))
                          : null
                        ))
                      }

                  </tbody>
                </table>
              </div>
            </div>
          : ''
        }

        <div className="shipments_menu_flex">
          <div className="shipments_menu_links">
            {
              this.state.allowedToEdit == 1 ? <a className="shipments_menu_link" href="edit.php?id=0"><i className="fa-solid fa-square-plus icon_btn_r21 icon_btn_blue"></i> <span>Добавить отправку</span></a> : null
            }
            <span className="archive_btn" name="showArchive" onClick={this.filterShow}><i className="fa-sharp fa-regular fa-box-archive icon_btn_r21 icon_btn_blue"></i>{this.state.showArchive == false ? "Архив" : "Все отправки"}</span>
          </div>
          <div className="shipments_menu_filters">
            <input type="text" value={this.state.filterNumber}  className=" filter shipment_filter_input" name="numberFilter" placeholder="№ отправки"  onChange={this.filterShow}/>
            <select
            value={this.state.filterDivision}
            className="filter shipments_filter_select"
            name="divisionFilter"
            onChange={this.filterShow}
            style=
            {{ display: this.state.isDivision == 1 ? 'none' : ''}}
            >
              <option id="division_0" value="0">Все подразделения</option>
              { this.state.apiData.divisionsList[0].map((division) => (
                <option value={division.id} id={"division_" + division.id } key={division.id}>{division.name}</option>
               )) }
            </select>
            <select value={this.state.filterStatus} className="filter shipments_filter_select" name="statusFilter" onChange={this.filterShow}>
              <option id="status_0" value="0">Все статусы</option>
              { this.state.apiData.shipmentStatuses[0].map((status) => ( <option value={status.id} id={"status_" + status.id } key={status.id}>{status.name}</option> ) ) }
            </select>
            <button onClick={this.filterShow} className="shipment_filter_button" name="clearFilters">Сбросить фильтры</button>
          </div>
        </div>
        <table id="shipmentsTable" className="table_list">
          <thead>
            <tr key="shipment_data_headers" className="table_tr">
              <td className="table_heading center table_td">№ отправки</td>
              <td className="table_heading center table_td">Список заявок</td>
              <td className="table_heading center table_td">Дата отправки</td>
              <td className="table_heading center table_td">Подразделение</td>
              <td className="table_heading center table_td">% выполнения</td>
              <td className="table_heading center table_td">Статус</td>
              <td className="table_heading center table_td">Накладная</td>
              <td className="table_heading center table_td">Действия</td>
            </tr>
            </thead>
            <tbody>
              {
                this.state.shipmentsToShow.map( (shipment) => (
                  shipment.id != 0
                  ? <tr
                    style=
                    {{ display:
                      this.state.isDivision == 1
                          ? this.state.divisionId == shipment.division
                            ? ''
                            : 'none'
                          : ''
                    }}
                    key={"shipment_data_" + shipment.id}
                    className="table_tr">
                      <td key={"shipment_number_" + shipment.id} className="flex_center table_td bold">
                      {this.state.allowedToEdit == 1
                        ? <a href={"edit.php?id=" + shipment.id}>{shipment.id}</a>
                        : <p>{shipment.id}</p>
                      }
                      </td>
                      <td key={"shipment_img_" + shipment.id} className="flex_center table_td">
					  
					  <i data-id={shipment.id}  className="fa-duotone fa-file-circle-info icon_btn_r17 icon_file_info table_img" key={shipment.id} name="openPopup"  onClick={this.showPopup}></i>
					  </td>

                      <td key={"shipment_date_" + shipment.id} className="flex_center table_td">
                      {shipment.textDate}
                      </td>
                      <td key={"shipment_division_" + shipment.id} className="flex_center table_td">{shipment.divisionName}</td>
                      <td key={"shipment_percent_" + shipment.id} className="flex_center table_td flex_center">
                        <div>
                          <div className="workout_green_div" style={{width: shipment.allJobsPercent}}></div>
                          <div className="workout_outer_div">
                            {shipment.countCurJobs} из {shipment.countAllJobs}
                            <br />
                            <span className="workout_perc_text">{shipment.allJobsPercent}%</span>
                          </div>
                        </div>
                      </td>
                      <td key={"shipment_status_" + shipment.id} className="flex_center table_td">
                        <select
                          name="statusChange"
                          className="filter shipments_filter_select"
                          onChange={this.filterShow} value={shipment.status}
                          data-ship={shipment.id} >
                          {
                            this.state.apiData.shipmentStatuses[0].map((stat, i)=>(
                              <option key={i} value={stat.id}>{stat.name}</option>
                            ))
                          }
                        </select>

                      </td>
                      <td key={"shipment_cons_" + shipment.id} className="flex_center table_td"><a target="_blank" href={"consignment.php?id=" + shipment.id}>Накладная</a></td>
                      <td key={"shipment_del_" + shipment.id} className="flex_center table_td">
                        <img data-app={shipment.id} title={this.state.showArchive == true ? 'Удалить' : 'В архив'} name={this.state.showArchive == true ? "deleteShipment" : "inArchive"} className="del_img" src="/acc/i/del.gif" onClick={this.filterShow} />
                        {this.state.showArchive == true
                        ? <img data-app={shipment.id} className="del_img" src="../../i/pr_ok.gif" title="Восстановить из архива" name="fromArchive" onClick={this.filterShow}/>
                        : null}
                        </td>
                    </tr>
                  : null
                ))
              }

          </tbody>
        </table>
      </div>
    );
  }
  }

}

//<img data-id={shipment.id} name="openPopup" key={shipment.id} className="table_img" src="../../../i/info_sm.png" onClick={this.showPopup}></img>
ReactDOM.render(
  <Shipments />,
  document.getElementById('app')
);
