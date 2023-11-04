<template>
    <div class="challo__card py-8 px-6 -mt-2">
        <Back :target="route('raffles.index')" :showModal="false" />
        <h5 class="page-title">Verlosungsdetails</h5>

        <div class="challo__card__body">

            <div class="detail__row">
                <div class="w-1/3">
                    <h4 class="detail__label">Verlosungs-ID</h4>
                    <span class="detail__value">
                        <a
                            target="_blank"
                            class="dropdown-item"
                            :href="route('raffles.show', { raffle : raffle.raffle_id } )"
                            v-if="hasPermission('sweepstakes.view')"
                        >
                            {{ raffle.raffle_prefix_id }}
                        </a>
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Gewinnspiel</h4>
                    <span class="detail__value">
                        <a
                            style="word-break: break-word;"
                            target="_blank"
                            class="dropdown-item underline"
                            :href="route('sweepstakes.show', { sweepstake : raffle.raffle_sweepstake_id })"
                            v-if="hasPermission('sweepstakes.view')"
                        >
                            {{ raffle.raffle_sweepstake_name }}
                        </a>
                    </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Art</h4>
                    <span class="detail__value">{{ sweepstake_type[raffle.raffle_sweepstake_type] }}</span>
                </div>
            </div>

            <div class="detail__row">
                <div class="w-1/3">
                    <h4 class="detail__label">Verlosungszeitpunkt</h4>
                    <span class="detail__value">{{ formateDate( raffle.raffle_time, 'DD.MM.YYYY&nbsp;&nbsp;HH:mm')}}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Anzahl Gewinner</h4>
                    <span class="detail__value">{{ raffle.raffle_number_of_winners }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Total Verlosungszahlstellen</h4>
                    <span class="detail__value">{{ raffle.raffle_total_sweepstake_number_position }}</span>
                </div>
            </div>

            <div class="detail__row">
                <div class="w-1/3">
                    <h4 class="detail__label">Gewinnzahlstellen</h4>
                    <span class="detail__value">{{ raffle.raffle_winning_number_position_from }} - {{ raffle.raffle_winning_number_position_to }} </span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Gewinnzahlen</h4>
                    <span class="detail__value text-primary-2 text-35">{{ raffle.raffle_winning_number }}</span>
                </div>
                <div class="w-1/3">
                    <h4 class="detail__label">Video</h4>
                    <VideoPlayIconButton
                        :video-name="raffle.raffle_video_src_path"
                        :show-label-text="true"
                    />
                </div>
            </div>

            <h6 class="text-primary-3 font-semibold mb-8 text-18x">Gewinner</h6>

            <!--            <div class="detail__row">

                            <div class="w-[45%]">
                                <h4 class="detail__label">100% Treffer</h4>
                                <Winner
                                    v-for="raffle_winner in raffle_winners['position_1']"
                                    :key="raffle_winner.raffle_winner_id"
                                    :raffle-winner="raffle_winner"
                                    :winnerRankNumber="1"
                                    :show-winner-text="false"
                                />
                                <span class="detail__value" v-if="raffle_winners['position_1'].length < 1">Keine</span>
                            </div>

                            <div class="w-[45%]">
                                <h4 class="detail__label">4 von 5 Treffer</h4>
                                <Winner
                                    v-for="raffle_winner in raffle_winners['position_2']"
                                    :key="raffle_winner.raffle_winner_id"
                                    :raffle-winner="raffle_winner"
                                    :winnerRankNumber="2"
                                />
                                <span class="detail__value" v-if="raffle_winners['position_2'].length < 1">Keine</span>
                            </div>

                        </div>-->

            <div class="detail__row" v-if="raffle_winners.length > 0">

                <div class="w-[45%]">
                    <h4 class="detail__label">100% Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[0]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="1"
                    />
                    <span class="detail__value" v-if="( raffle_winners[0].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -1) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -1) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[1]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="2"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -1) > 0 && raffle_winners[1].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 2">

                <div class="w-[45%]" v-if="(maxWinningNumber -2) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -2) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[2]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="3"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -2) > 0 && raffle_winners[2].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -3) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -3) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[3]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="4"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -3) > 0 && raffle_winners[3].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 4">

                <div class="w-[45%]" v-if="(maxWinningNumber -4) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -4) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[4]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="5"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -4) > 0 && raffle_winners[4].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -5) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -5) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[5]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="6"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -3) > 0 && raffle_winners[5].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 6">

                <div class="w-[45%]" v-if="(maxWinningNumber -6) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -6) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[6]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="7"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -6) > 0 && raffle_winners[6].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -7) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -7) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[7]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="8"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -7) > 0 && raffle_winners[7].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 8">

                <div class="w-[45%]" v-if="(maxWinningNumber -8) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -8) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[8]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="9"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -8) > 0 && raffle_winners[8].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -9) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -9) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[9]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="10"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -9) > 0 && raffle_winners[9].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 10">

                <div class="w-[45%]" v-if="(maxWinningNumber -10) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -10) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[10]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="11"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -10) > 0 && raffle_winners[10].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -11) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -11) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[11]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="12"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -11) > 0 && raffle_winners[11].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 12">

                <div class="w-[45%]" v-if="(maxWinningNumber -12) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -12) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[12]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="13"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -12) > 0 && raffle_winners[12].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -13) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -13) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[13]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="14"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -13) > 0 && raffle_winners[13].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 14">

                <div class="w-[45%]" v-if="(maxWinningNumber -14) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -14) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[14]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="15"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -14) > 0 && raffle_winners[14].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -15) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -15) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[15]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="16"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -15) > 0 && raffle_winners[15].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 16">

                <div class="w-[45%]" v-if="(maxWinningNumber -16) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -16) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[16]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="17"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -16) > 0 && raffle_winners[16].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -17) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -17) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[17]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="18"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -17) > 0 && raffle_winners[17].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 18">

                <div class="w-[45%]" v-if="(maxWinningNumber -18) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -18) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[18]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="19"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -18) > 0 && raffle_winners[18].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -19) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -19) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[19]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="20"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -19) > 0 && raffle_winners[19].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 20">

                <div class="w-[45%]" v-if="(maxWinningNumber -20) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -20) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[20]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="21"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -20) > 0 && raffle_winners[20].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -21) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -21) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[21]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="22"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -21) > 0 && raffle_winners[21].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 22">

                <div class="w-[45%]" v-if="(maxWinningNumber -22) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -22) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[22]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="23"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -22) > 0 && raffle_winners[22].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -23) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -23) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[23]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="24"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -23) > 0 && raffle_winners[23].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 24">

                <div class="w-[45%]" v-if="(maxWinningNumber -24) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -22) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[24]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="25"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -24) > 0 && raffle_winners[24].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -25) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -25) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[25]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="26"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -25) > 0 && raffle_winners[25].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 26">

                <div class="w-[45%]" v-if="(maxWinningNumber -26) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -26) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[26]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="27"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -26) > 0 && raffle_winners[26].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -27) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -27) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[27]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="28"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -27) > 0 && raffle_winners[27].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 28">

                <div class="w-[45%]" v-if="(maxWinningNumber -28) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -28) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[28]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="29"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -28) > 0 && raffle_winners[28].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -29) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -29) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[29]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="30"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -29) > 0 && raffle_winners[29].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 30">

                <div class="w-[45%]" v-if="(maxWinningNumber -30) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -30) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[30]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="31"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -30) > 0 && raffle_winners[30].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -31) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -31) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[31]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="32"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -31) > 0 && raffle_winners[31].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 32">

                <div class="w-[45%]" v-if="(maxWinningNumber -32) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -32) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[32]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="33"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -32) > 0 && raffle_winners[32].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -33) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -33) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[33]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="34"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -33) > 0 && raffle_winners[33].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 34">

                <div class="w-[45%]" v-if="(maxWinningNumber -34) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -34) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[34]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="35"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -34) > 0 && raffle_winners[34].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -35) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -35) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[35]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="36"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -35) > 0 && raffle_winners[35].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 36">

                <div class="w-[45%]" v-if="(maxWinningNumber -36) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -36) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[36]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="37"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -36) > 0 && raffle_winners[36].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -37) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -37) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[37]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="38"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -37) > 0 && raffle_winners[37].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="raffle_winners.length > 38">

                <div class="w-[45%]" v-if="(maxWinningNumber -38) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -38) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[38]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="39"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -38) > 0 && raffle_winners[38].length === 0 )">Keine</span>
                </div>

                <div class="w-[45%]" v-if="(maxWinningNumber -39) > 0">
                    <h4 class="detail__label">{{ (maxWinningNumber -39) }} von {{ maxWinningNumber }} Treffer</h4>
                    <Winner
                        v-for="raffle_winner in raffle_winners[39]"
                        :key="raffle_winner.raffle_winner_id"
                        :raffle-winner="raffle_winner"
                        :winnerRankNumber="40"
                        :show-winner-text="false"
                    />
                    <span class="detail__value" v-if="( (maxWinningNumber -39) > 0 && raffle_winners[39].length === 0 )">Keine</span>
                </div>

            </div>

            <div class="detail__row" v-if="hasPermission('raffles.edit') && redraw_active">
                <div class="w-[33%]">
                    <div class="input-wrapper">
                        <button class="btn-block challo__btn btn-primary" type="button" @click="showStreamingModal">
                            <VideoPlayIcon
                                :show-label-text="true"
                                label-text="Verlosung starten"
                            />
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import Back from "../../Components/Form/Back.vue";
import Winner from "./Components/Winner";
import VideoPlayIconButton from "./Components/VideoPlayIconButton";
import VideoStreamingIconButton from "./Components/VideoStreamingIconButton";
import {computed, reactive, ref} from "@vue/reactivity";
// import VideoRecorder from "./Components/VideoRecorder";
import VideoRecorder from "./Components/LiveStreaming.vue";
import RaffleWinnerCapture from "./Components/RaffleWinnerCapture";
import {inject} from "vue";
import VideoPlayIcon from "./Components/VideoPlayIcon";
//import Video abspielen

export default {
    components: {
        VideoPlayIcon,
        Back,
        Winner,
        VideoPlayIconButton,
        computed,
        reactive,
        ref,
        VideoStreamingIconButton,
    },

    props: {
        raffle: {
            type: Object,
            required: true,
        },

        redraw_active: {
            type: Boolean,
            default: false,
        },

        raffle_winners: {
            type: [Array, Object],
            default: []
        },

        loopCount: {
            type: Number,
            default: 0
        },

        sweepstake_type: {
            type: Object,
            default: {
                'sweepstake' : 'Gewinnspiel',
                'jackpot' : 'Jackpot',
            }
        }
    },

    setup(props) {
        const modal = inject("modal");
        const maxWinningNumber = ( parseInt( props.raffle.raffle_winning_number_position_to ) - parseInt( props.raffle.raffle_winning_number_position_from ) + 1 );

        return {
            maxWinningNumber,
            modal,
        }
    },

    methods: {
        showStreamingModal() {
            let modal = this.modal;
            //console.log( this.$props.raffle )
            modal.show(VideoRecorder, {
                props: {
                    streamId: this.raffle.raffle_id,
                },
                config: {
                    staticBackdrop: true,
                    overlayStyle: "background: rgb(120, 120, 120, 0.5)",
                },
                events: {
                    streamComplete: (videoFileName) => {
                        //modal.hide();
                        modal.show( RaffleWinnerCapture, {
                            props: {
                                video_src_path: videoFileName,
                                raffle_id: this.raffle.raffle_id,
                                winning_number_position: this.raffle.raffle_total_sweepstake_number_position,
                            },

                            config: {
                                staticBackdrop: true,
                                overlayStyle: "background: rgb(120, 120, 120, 0.5)",
                            },

                            events: {
                                winnerCLick: () => {
                                    window.location.reload();
                                }
                            }
                        } )
                    },
                    close: modal.hide
                },
            });
        }
    }
}

</script>
