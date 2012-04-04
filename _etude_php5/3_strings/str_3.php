<?php
# printf
// ----3.55

setlocale(LC_NUMERIC, 'french');
printf("%'-4.8f\n", 3.553);
printf("%'*2d\n", 3.5553);
printf("%b\n", 18.5553);
printf("%d and %.2f makes %o\n",
4+7, 4/7, 47);