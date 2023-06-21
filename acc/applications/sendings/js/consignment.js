class Invoice extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      isLoaded: false,     // Флаг загрузки данных
      error: null,
      data: ''
    }
    // Functions bind
  }

  componentDidMount() {
  var id = document.getElementById('invoiceScript').attributes[1].value;
  fetch("invoice_info.php?id=" + id)
  .then(res => res.json())
  .then((result) => {
        this.setState({
          isLoaded: true,
          data: result,
        });
        console.log(this.state.data);
      },
      (error) => {
        this.setState({
          isLoaded: true,
          error
        });
      }
    )
  }

  render() {
    const { error, isLoaded, data } = this.state;
    if (error) {
      return <div>Error: {error.message}</div>;
    } else if (!isLoaded) {
      return <div><img src="../../../i/load2.gif"/></div>;
    } else {
      return(
      <div className="edit_cont">
          {
            this.state.data.id !== 0
            ? <div className="edit_cont_body">
                <a href="index.php" className="shipments_menu_link">К списку отправок</a>
                <br />
                <h1>Список материалов для отправки № {this.state.data.id}</h1>
                <br />
                <div className="invoice_subheading">Список заявок</div>
                <br />
                <div className="applications_list">
                  <TableHeadings />
                  {
                    this.state.data.applications.map((app, i) => (
                      <ApplicationRow
                        key={i}
                        id={app.numOrd}
                        bottomStrength={app.bottomStrength}
                        paperNumList={app.paperNumList}
                        paperListSize={app.paperListSize}
                        ruchki={app.ruchki}
                        ruchkiLength={app.ruchkiLength}
                        ruchkiColor={app.ruchkiColor}
                        scotch={app.scotch}
                        scotchType={app.scotchType}
                        sideStrength={app.sideStrength}
                        stickers={app.stickers}
                        tiraz={app.tiraz}
                        uid={app.uid}
                        podruchniki={app.podruchniki}
                        podruchnikiSizeName={app.podruchnikiSizeName}
                        ruchkiColor={app.ruchkiColor}
                        kley={app.kley}
                        stretch={app.stretch}
                        shir={app.shir}
                        bok={app.bok}
                      />
                    ))
                  }
                </div>
                <br />
                <div className="invoice_subheading">Итого</div>
                <br />
                <TotalList
                bottomStrength={this.state.data.bottomStrength}
                sideStrength={this.state.data.sideStrength}
                totalSkotch={this.state.data.totalSkotch}
                totalKley={this.state.data.totalKley}
                uniquePodruchniki={this.state.data.uniquePodruchniki}
                uniquePodruchnikiQuant={this.state.data.uniquePodruchnikiQuant}
                uniqueRuchki={this.state.data.uniqueRuchki}
                uniqueRuchkiQuant={this.state.data.uniqueRuchkiQuant}
                totalStretch={this.state.data.totalStretch}
                totalPaperList={this.state.data.totalPaperList}
                uniqueListQuant={this.state.data.uniqueListQuant}
                uniqueList={this.state.data.uniqueList}
                uniqueScotch={this.state.data.uniqueScotch}
                uniqueScotchQuant={this.state.data.uniqueScotchQuant}
                uniqueBottom={this.state.data.uniqueBottom}
                uniqueBottomQuant={this.state.data.uniqueBottomQuant}
                />
              </div>
            : ''
          }
      </div>
      );
    }
  }

}

function TableHeadings() {
  return(
    <div className="application_table">
      <div className="material_table_column">
        <span className="app_table_heading"><b>№ заявки</b></span>
      </div>
      <div className="material_table_column">
        <span className="app_table_heading"><b>Тираж</b></span>
      </div>
      <div className="material_table_column">
        <span className="app_table_heading"><b><span className="app_table_heading_min">Материал</span></b></span>
      </div>
      <div className="material_table_column">
        <span className="app_table_heading"><b>Количество материалов</b></span>
      </div>
    </div>
  );
}

function ApplicationRow(props) {
    return(
      <div className="application_table">
        <div className="material_table_column">
          <div className="app_table_heading">{props.id}</div>
        </div>
        <div className="material_table_column">
          <div className="app_table_heading">{props.tiraz}</div>
        </div>
        <div className="material_table_column">
          <div>
            <div className="material_list"><p>Укрепление дна (шир {props.shir}см, бок {props.bok}см)</p></div>
            <div className="material_list">
              <p>Ручки {props.ruchkiLength !== 0 ? props.ruchkiLength + ' см ' : ''}  {props.ruchkiColor !== 0 ? '(' + props.ruchkiColor + ')' : ''}</p>
            </div>
            <div className="material_list"><p>Подручники ({props.podruchnikiSizeName})</p></div>
            {props.scotch != 0 ? <div className="material_list"><p>Скотч {props.scotchType}</p></div> : null}
            <div className="material_list"><p>Клей</p></div>
            <div className="material_list"><p>Стретч-пленка</p></div>
            <div className="material_list"><p>Вырубленные листы  </p></div>
          </div>
        </div>
        <div className="material_table_column">
          <div>
            <div className="material_list"><p>{props.bottomStrength} шт</p></div>
            <div className="material_list"><p>{props.ruchki} шт</p></div>
            <div className="material_list"><p>{props.podruchniki} шт</p></div>
            {props.scotch != 0 ? <div className="material_list"><p>{props.scotch} м</p></div> : null}
            <div className="material_list"><p>{props.kley} кг</p></div>
            <div className="material_list"><p>{props.stretch} кг</p></div>
            <div className="material_list"><p>{props.paperNumList} шт</p></div>
          </div>
        </div>
      </div>
    );
}

function TotalList(props) {
  console.log(props);
  return(
    <div className="applications_list">
      <div className="application_table">
        <div className="material_table_column">
          <span className="app_table_heading"><b>Материалы</b></span>
        </div>
        <div className="material_table_column">
          <span className="app_table_heading"><b>Количество</b></span>
        </div>
      </div>
      <div className="application_table">
        <div className="material_table_column">
          <div>
            {
              props.uniqueBottom.map((elem, i)=> (
                <div key={i} className="material_list"><p>Укрепление дна (шир {elem.split('-')[0]}см, {elem.split('-')[1]}см)</p></div>
              ))
            }

            {
              props.uniqueScotch.map((elem, i)=>(
                <div key={i} className="material_list"><p>Скотч {elem}</p></div>
              ))
            }
            {
              props.uniqueRuchki.map((elem, i)=>(
                <div key={i} className="material_list">
                  <p>Ручки {elem.split('-')[0] !== '0' ? elem.split('-')[0] + ' см ' : '' } {elem.split('-')[1] !== '0' ? '(' + elem.split('-')[1] + ')' : ''}</p>
                </div>
               ))
            }
            {
              props.uniquePodruchniki.map((elem, i)=>(
                <div key={i} className="material_list"><p>Подручники (высота {elem.split('-')[0]} см, ширина {elem.split('-')[1]} см)</p></div>
              ))
            }
            <div className="material_list"><p>Клей</p></div>
            <div className="material_list"><p>Стретч-пленка</p></div>
            <div className="material_list"><p>Вырубленные листы</p></div>

          </div>
        </div>
        <div className="material_table_column">
          <div>
            {
              props.uniqueBottomQuant.map((elem, i)=>(
                <div key={i} className="material_list"><p>{elem} шт</p></div>
              ))
            }

            {
              props.uniqueScotchQuant.map((elem, i)=>(
                <div key={i} className="material_list"><p>{elem} м</p></div>
              ))
            }
            {
              props.uniqueRuchkiQuant.map((elem, i)=>(
                <div key={i} className="material_list"><p>{elem} шт</p></div>
              ))
            }
            {
              props.uniquePodruchnikiQuant.map((elem, i)=>(
                <div key={i} className="material_list"><p>{elem} шт</p></div>
              ))
            }
            <div className="material_list"><p>{props.totalKley} кг</p></div>
            <div className="material_list"><p>{props.totalStretch} кг</p></div>
            <div className="material_list"><p>{props.totalPaperList} шт</p></div>

          </div>
        </div>
      </div>
    </div>
  );
}

ReactDOM.render(
  <Invoice />,
  document.getElementById('app')
);
