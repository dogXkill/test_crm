<?php

class MoneyHelper
{

    public static function format($amount)
    {
        $amount = preg_replace('/\,/', '.', ''.$amount);
        $amount = number_format($amount, 2, '.', '');
        $amount = preg_replace('/\.00/', '', $amount);
        $amount = preg_replace('/-0/', '0', $amount);

        return $amount;
    }

    /**
     * �������� ����������
     */
    public static function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20) {
            return $f5;
        }
        $n = $n % 10;
        if ($n > 1 && $n < 5) {
            return $f2;
        }
        if ($n == 1) {
            return $f1;
        }

        return $f5;
    }

    /**
     * ���������� ����� ��������
     * @uses morph()
     */
    public static function num2str($num)
    {
        $nul = '����';
        $ten = [
            ['', '����', '���', '���', '������', '����', '�����', '����', '������', '������'],
            ['', '����', '���', '���', '������', '����', '�����', '����', '������', '������'],
        ];
        $a20 = [
            '������', '�����������', '����������', '����������', '������������', '����������', '�����������',
            '����������', '������������', '������������',
        ];
        $tens = [
            2 => '��������', '��������', '�����', '���������', '����������', '���������', '�����������', '���������',
        ];
        $hundred = [
            '', '���', '������', '������', '���������', '�������', '��������', '�������', '���������', '���������',
        ];
        $unit = [
            ['�������', '�������', '������', 1],
            ['�����', '�����', '������', 0],
            ['������', '������', '�����', 1],
            ['�������', '��������', '���������', 0],
            ['��������', '���������', '����������', 0],
        ];

        list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
        $out = [];

        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) {
                if (!intval($v)) {
                    continue;
                }
                $uk = sizeof($unit) - $uk - 1;
                $gender = $unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = $hundred[$i1]; // 1xx-9xx
                if ($i2 > 1) {
                    $out[] = $tens[$i2].' '.$ten[$gender][$i3];
                } // 20-99
                else {
                    $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3];
                } // 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1) {
                    $out[] = self::morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
                }
            }
        } else {
            $out[] = $nul;
        }

        $out[] = self::morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
        $out[] = $kop.' '.self::morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop

        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }
}


