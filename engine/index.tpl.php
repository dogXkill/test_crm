<?=$menu;?>
<div id="customersCallsPage">
    <div id="filters">
        <div class="clear">
            <div id="filter_search" data-filter-id="search" class="filter filterType_input<?php if (isset($filters['search'])) echo ' active'; ?>">
                <div class="filter_input">
                    <input type="text"<?php if (isset($filters['search'])) echo 'value="' . $filters['search'] . '"'; ?> class="textInput" placeholder="Поиск по названию">
                </div>
            </div>

            <div id="filter_manager" data-filter-id="manager"<?php if (isset($filters['manager'])) echo 'data-filter-value="' . $filters['manager'] . '"'; ?> class="filter filterType_select<?php if (isset($filters['manager'])) echo ' active'; ?>">
                <div class="filter__input">
                    <div class="filter__button clear">
                        <span class="filter__button_label" data-placeholder="Мендеджер"><?php echo isset($filters['manager']) ? $managers[$filters['manager']]['surname'] : 'Мендеджер'; ?></span>
                        <span class="filter__button_arrow"><i class="fa fa-arrow-down"></i></span>
                        <span class="filter__button_reset"><i class="fa fa-times"></i></span>
                    </div>
                    <div class="filter__options">
                        <?php

                            $activeGroup = 'active';
                            $buttonGroup = 'archive';

                            if (isset($filters['manager']) && isset($managers[$filters['manager']])) {
                                if ($managers[$filters['manager']]['archive']) {
                                    $activeGroup = 'archive';
                                    $buttonGroup = 'active';
                                }
                            }

                            $groups = array(
                                'archive' => array(
                                    'label' => '<i class="fa fa-times"></i>Архивные',
                                    'next' => 'active'
                                ),
                                'active' => array(
                                    'label' => '<i class="fa fa-check"></i>Активные',
                                    'next' => 'archive'
                                )
                            );
                        ?>
                        <div class="filter__switchGroup" data-groups='<?=json_encode($groups);?>' data-show-group="<?=$buttonGroup;?>" data-current-group="<?=$activeGroup;?>"><?=$groups[$buttonGroup]['label'];?></div>
                        <?php foreach ($managers as $manager_id => $manager) { ?>
                        <?php
                            $class = '';
                            if ($manager['archive']) {
                                $class = 'group-archive';
                                if ($activeGroup == 'archive') {
                                    $class .= ' showed';
                                }
                            } else {
                                $class = 'group-active';
                                if ($activeGroup == 'active') {
                                    $class .= ' showed';
                                }
                            }

                            if (isset($filters['manager']) && $filters['manager'] == $manager_id) {
                                $class .= ' active';
                            }
                        ?>
                        <div class="filter__option <?=$class;?>" data-value="<?php echo $manager_id; ?>"><?php echo $manager['surname']; ?></div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div id="filter_orderType" data-filter-id="orderType"<?php if (isset($filters['orderType'])) echo 'data-filter-value="' . $filters['orderType'] . '"'; ?> class="filter filterType_select<?php if (isset($filters['orderType'])) echo ' active'; ?>">
                <div class="filter__input">
                    <div class="filter__button clear">
                        <span class="filter__button_label" data-placeholder="Тип заказа"><?php echo isset($filters['orderType']) ? QueriesConfig::$filter_orderType[$filters['orderType']] : 'Тип заказа'; ?></span>
                        <span class="filter__button_arrow"><i class="fa fa-arrow-down"></i></span>
                        <span class="filter__button_reset"><i class="fa fa-times"></i></span>
                    </div>
                    <div class="filter__options">
                        <?php foreach (QueriesConfig::$filter_orderType as $key => $value) { ?>
                        <div class="filter__option showed<?php if (isset($filters['orderType']) && $filters['orderType'] == $key) echo ' active'; ?>" data-value="<?php echo $key; ?>"><?php echo $value; ?></div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div id="filter_ordersCount" data-filter-id="ordersCount"<?php if (isset($filters['ordersCount'])) echo 'data-filter-value="' . $filters['ordersCount'] . '"'; ?> class="filter filterType_num<?php if (isset($filters['ordersCount'])) echo ' active'; ?>">
                <div class="filter_input">
                    Кол-во заказов &ge; <input type="text"<?php if (isset($filters['ordersCount'])) echo 'value="' . $filters['ordersCount'] . '"'; ?> maxlength="3" class="textInput" placeholder="0">
                </div>
            </div>


            <div id="filter_noCalls" data-filter-id="noCalls" class="filter filterType_checkbox<?php if (isset($filters['noCalls']) && $filters['noCalls']) echo ' active'; ?>">
                <div class="filter_input">
                    <div class="filter__button clear">
                        <span class="filter__button_label">Клиенты без звонков</span>
                        <span class="filter__button_ico"><i class="fas fa-phone-slash"></i></span>
                    </div>
                </div>
            </div>

            <?php

                $periodFrom = false;
                $periodTo = false;

                if (isset($filters['ordersPeriodFrom'])) {
                    $periodFrom = date('d.m.Y', strtotime($filters['ordersPeriodFrom']));
                }

                if (isset($filters['ordersPeriodTo'])) {
                    $periodTo = date('d.m.Y', strtotime($filters['ordersPeriodTo']));
                }

            ?>

            <div id="filter_ordersPeriod" data-filter-id="ordersPeriod" class="filter filterType_period<?php if ($periodFrom && $periodTo) echo ' active'; ?>">
                <div class="filter_input">
                    Последний заказ с <span class="periodRange periodRange_from<?php if ($periodFrom) echo ' active'; ?>"><input type="text" value="<?php echo $periodFrom ? $periodFrom : '—'; ?>" data-placeholder="—" readonly /><span class="periodRange_arrow"><i class="fa fa-arrow-down"></i></span></span> по <span class="periodRange periodRange_to<?php if ($periodTo) echo ' active'; ?>"><input type="text" value="<?php echo $periodTo ? $periodTo : '—'; ?>" data-placeholder="—" readonly /><span class="periodRange_arrow"><i class="fa fa-arrow-down"></i></span></span>
                </div>
            </div>

            <?php

                $periodFrom = false;
                $periodTo = false;

                if (isset($filters['callsPeriodFrom'])) {
                    $periodFrom = date('d.m.Y', strtotime($filters['callsPeriodFrom']));
                }

                if (isset($filters['callsPeriodTo'])) {
                    $periodTo = date('d.m.Y', strtotime($filters['callsPeriodTo']));
                }

            ?>

            <div id="filter_callsPeriod" data-filter-id="callsPeriod" class="filter filterType_period<?php if ($periodFrom && $periodTo) echo ' active'; ?>">
                <div class="filter_input">
                    Последний звонок с <span class="periodRange periodRange_from<?php if ($periodFrom) echo ' active'; ?>"><input type="text" value="<?php echo $periodFrom ? $periodFrom : '—'; ?>" data-placeholder="—" readonly /><span class="periodRange_arrow"><i class="fa fa-arrow-down"></i></span></span> по <span class="periodRange periodRange_to<?php if ($periodTo) echo ' active'; ?>"><input type="text" value="<?php echo $periodTo ? $periodTo : '—'; ?>" data-placeholder="—" readonly /><span class="periodRange_arrow"><i class="fa fa-arrow-down"></i></span></span>
                </div>
            </div>


        </div>

        <div class="clear">
            <div class="topPagination clear">
                <div id="countingBox">Найдено клиентов: <span><?=$customersCount;?></span></div>
                <div id="resetFiltersButton">Сбросить фильтры <i class="fas fa-redo"></i></div>
                <div class="paginationLinks">
                    <?php echo $pagination; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="listBox">
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $.datepicker.regional['ru'] = {
                    closeText: 'Закрыть',
                    prevText: '&#x3c;Пред',
                    nextText: 'След&#x3e;',
                    currentText: 'Сегодня',
                    monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
                    'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
                    monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
                    'Июл','Авг','Сен','Окт','Ноя','Дек'],
                    dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
                    dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
                    dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
                    weekHeader: 'Нед',
                    dateFormat: 'dd.mm.yy',
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: false,
                    yearSuffix: ''
                };

                $.datepicker.setDefaults($.datepicker.regional['ru']);

                $.timepicker.regional['ru'] = {
                    timeOnlyTitle: 'Выберите время',
                    timeText: 'Время',
                    hourText: 'Часы',
                    minuteText: 'Минуты',
                    secondText: 'Секунды',
                    millisecText: 'Миллисекунды',
                    timezoneText: 'Часовой пояс',
                    currentText: 'Сейчас',
                    closeText: 'Закрыть',
                    timeFormat: 'HH:mm',
                    amNames: ['AM', 'A'],
                    pmNames: ['PM', 'P'],
                    isRTL: false
                };
                $.timepicker.setDefaults($.timepicker.regional['ru']);
            });
        </script>

        <table id="customersList" cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
            <thead>
                <tr>
                    <td class="col-clientShort">Клиент</td>
                    <td class="col-clientFull">Контакт</td>
                    <td class="col-manager">Менеджер</td>
                    <td class="col-orders">Заказы</td>
                    <td class="col-lastOrder">Последний заказ</td>
                    <td class="col-orderType">Тип заказа</td>
                    <td class="col-lastCall">Последний звонок</td>
                    <td class="col-actions"></td>
                </tr>
            </thead>
            <tbody>
                <?php echo $items; ?>
            </tbody>
        </table>
    </div>

    <div class="bottomPagination clear">
        <div class="paginationLinks">
            <?php echo $pagination; ?>
        </div>
    </div>

    <div id="editCallsDialog" class="paketoffDialog" title="Список звонков">
        <div class="dialogContent"></div>
        <div class="dialogLoader"><div></div></div>
    </div>
</div>