import Vue from 'vue';
const { Howl } = require('howler');
import { mapGetters } from 'vuex';
import bitcoin from 'bitcoin-units';
import Bus from '../bus';

Vue.mixin({
    mounted() {
        this.$store.dispatch('setGameInstance', {
            game: null,
            history: null,
            bettingType: 'manual',
            playTimeout: false
        });

        const validateMultiplayerAction = (game_id, event, data, log = true) => {
            if(this.gameInstance.game && this.gameInstance.game.id === game_id) {
                if(log && this.$isDebug()) console.log(`mWS ${event}`, data);
                if(window.$gameRef && window.$gameRef.multiplayerEvent) window.$gameRef.multiplayerEvent(event, data);
            }
        };

        if(!window.$multiplayerEventHandler) {
            Bus.$on('event:multiplayerBettingStateChange', (e) => validateMultiplayerAction(e.game, 'MultiplayerBettingStateChange', e));
            Bus.$on('event:multiplayerBetCancellation', (e) => validateMultiplayerAction(e.game.game, 'MultiplayerBetCancellation', e));
            Bus.$on('event:multiplayerGameFinished', (e) => validateMultiplayerAction(e.game, 'MultiplayerGameFinished', e));
            Bus.$on('event:multiplayerGameBet', (e) => validateMultiplayerAction(e.game.game, 'MultiplayerGameBet', e));
            Bus.$on('event:multiplayerDataUpdate', (e) => validateMultiplayerAction(e.game, 'MultiplayerDataUpdate', e, false));
            Bus.$on('event:multiplayerTimerStart', (e) => {
                validateMultiplayerAction(e.game, 'MultiplayerTimerStart', e);
                if (this.gameInstance.game && this.gameInstance.bettingType === 'auto' && this.gameInstance.game.id === e.game && this.gameInstance.game.autoBetSettings.state)
                    this.gameInstance.game.autoBetSettings.next();
            });
            Bus.$on('event:liveGame', (e) => {
                if (this.gameInstance.game && this.user && this.user.user._id === e.user._id && this.gameInstance.game && this.gameInstance.game.autoBetSettings.state
                    && this.gameInstance.bettingType === 'auto' && e.game.game === this.gameInstance.game.id && e.game.type === 'multiplayer')
                     setTimeout(() => this.gameInstance.game.autoBetSettings.nextGameHandler(e.game.status === 'win'), e.delay);
            });

            window.$multiplayerEventHandler = true;
        }
    },
    computed: {
        ...mapGetters(['gameInstance', 'games', 'user', 'unit', 'sound', 'demo', 'currencies', 'currency', 'usd'])
    },
    data() {
        return {
            soundTimeouts: []
        }
    },
    methods: {
        createGameInstance(id, ref) {
            let instance = this.gameInstance ?? {
                game: null,
                history: null,
                bettingType: 'manual',
                playTimeout: false
            };
            instance.game = {
                id: id,
                data: {},
                autoBetSettings: {
                    supported: false,
                    state: false,
                    games: 0,
                    stopOnWin: false,
                    currentIteration: 0,
                    initialBet: 0,
                    timeout: 0,
                    win: {
                        action: 'reset',
                        value: 0
                    },
                    loss: {
                        action: 'reset',
                        value: 0
                    }
                },
                bettingType: 'manual',
            };
            this.$store.dispatch('setGameInstance', instance);

            if(ref.restore) axios.post('/api/game/restore', { api_id: id }).then((data) => {
				if(data.data.code != null) return;
                this.updateGameInstance((i) => {
                    i.game.extendedId = data.data.game._id;
                    i.game.extendedState = 'in-progress';
                    i.game.profit = null;
                    i.game.currentProfit = this.rawBitcoin(data.data.game.currency, data.data.game.wager * data.data.game.multiplier);
                    i.playTimeout = false;
                    i.bet = data.data.game.wager;
                });
                ref.restore(data.data);
            });

            if(ref.gameDataRetrieved) axios.post('/api/game/data/' + id).then((data) => {
                instance = this.gameInstance;
                instance.game.data = data.data;
                this.$store.dispatch('setGameInstance', instance);
                ref.gameDataRetrieved(instance.game.data);
            });

            if(ref.afterMount) ref.afterMount();
        },
        capitalize(string) {
            return [].map.call(string, (char, i) => i ? char : char.toUpperCase()).join('');
        },
        applyHouseEdge(payout) {
            const game = this.games.filter((e) => e.id === this.gameInstance.game.id)[0];
            return game.houseEdge ? (payout * (1 - game.houseEdge / 100)).toFixed(2) : payout.toFixed(2);
        },
        countdown(element, { timeLimit = 15, timePassed, warningThreshold = 10, alertThreshold = 5 }, callback) {
            const fullDashArray = 283, colorCodes = {
                info: { color: "green" },
                warning: { color: "orange", threshold: warningThreshold },
                alert: { color: "red", threshold: alertThreshold }
            };

            let timeLeft = timeLimit, remainingPathColor = colorCodes.info.color;

            $(element).fadeIn('fast').html(`
                <div class="multiplayerTimer">
                    <svg viewBox="0 0 100 100">
                        <g class="circle">
                            <circle class="path-elapsed" cx="50" cy="50" r="45"></circle>
                            <path stroke-dasharray="283" class="path-remaining ${remainingPathColor}" d="M 50, 50 m -45, 0 a 45,45 0 1,0 90,0 a 45,45 0 1,0 -90,0"></path>
                        </g>
                    </svg>
                    <span class="label ${remainingPathColor}">${formatTime(timeLeft)}</span>
                </div>
            `);

            startTimer();

            function onTimesUp() {
                clearInterval(window.timerInterval);
            }

            function startTimer() {
                const update = () => {
                    timeLeft = timeLimit - timePassed;
                    $(element).find('.label').html(formatTime(timeLeft));
                    setCircleDasharray();
                    setRemainingPathColor(timeLeft);
                    if (timeLeft === 0) onTimesUp();
                };

                window.timerInterval = setInterval(() => {
                    timePassed++;
                    update();
                }, 1000);
                update();
            }

            function formatTime(time) {
                let minutes = Math.floor(time / 60), seconds = time % 60;
                if (seconds < 10) seconds = `0${seconds}`;
                return `${minutes}:${seconds}`;
            }

            function setRemainingPathColor(timeLeft) {
                const { alert, warning, info } = colorCodes;
                if (timeLeft <= alert.threshold) $(element).find('.path-remaining, .label').removeClass(warning.color).addClass(alert.color);
                else if (timeLeft <= warning.threshold) $(element).find('.path-remaining, .label').removeClass(info.color).addClass(warning.color);
            }

            function calculateTimeFraction() {
                const rawTimeFraction = timeLeft / timeLimit;
                return rawTimeFraction - (1 / timeLimit) * (1 - rawTimeFraction);
            }

            function setCircleDasharray() {
                const circleDasharray = `${(timeLeft <= 0 ? 0 : calculateTimeFraction() * fullDashArray).toFixed(0)} 283`;
                $('.path-remaining').attr('stroke-dasharray', circleDasharray);
                if(timeLeft <= 0) $(element).fadeOut('fast', callback);
            }
        },
        rawBitcoin(to, value) {
            if(to.startsWith('local_')) return (value ? value : 0).toFixed(2);
            return bitcoin(value, 'btc').to(this.unit).value().toFixed(this.unit === 'satoshi' ? 0 : 8);
        },
        usdToToken(price, usd) {
            return usd / price;
        },
        tokenToUsd(price, token) {
            return token * price;
        },
        sendTurn(data, callback, finishedCallback = null) {
            axios.post('/api/game/turn', {
                id: this.extendedGameId(),
                data: data
            }).then((data) => {
                callback(data.data);

                if(data.data.type === 'fail') {
                    console.error('Failed turn', data.data);
                    return;
                }

                if(this.gameInstance.bettingType === 'manual' && data.data.type === 'continue')
                    this.updateGameInstance((i) => i.game.currentProfit = (i.bet * data.data.game.multiplier).toFixed(this.usd ? 2 : (this.currency.startsWith('local_') ? 2 :8)));
                if(data.data.type === 'lose' || data.data.type === 'finish') this.pushStats(data.data.game);
            }, (error) => {
                switch (error) {
                    case 0:
                        this.sendTurn(data, callback, finishedCallback);
                        break;
                    case 1:
                        this.$toast.error('Invalid game id');
                        break;
                    case 2:
                        if(finishedCallback != null) finishedCallback();
                        break;
                    case 3:
                        this.$toast.error('Invalid API operation');
                        break;
                }
            })
        },
        playSound(src, timeout = null) {
            if(timeout != null) {
                let cancel = false;
                _.forEach(this.soundTimeouts, function(t) {
                    if(t.src === src && +new Date() < t.time) cancel = true;
                });

                if(cancel) return;

                this.soundTimeouts.push({
                    'src': src,
                    'time': +new Date() + timeout
                });
            }

            if(!this.sound) return;
            new Howl({ src: [src], volume: 0.3 }).play();
        },
        chain(times, ms, callback) {
            let i = 0;

            const next = () => {
                if(i < times) {
                    setTimeout(() => {
                        callback(i);
                        next();
                    }, ms);
                    i++;
                }
            };

            next();
        },
        resultPopup(game) {
            if(game.status !== undefined && game.status === 'cancelled') return;

            const status = game.profit === 0 && game.multiplier === 0 ? 'lose' : (game.status === undefined ? (game.win ? 'win' : 'lose') : (game.status === 'lose' ? 'lose' : 'win'));
            const resultPopup = $(`<div class="resultPopup resultPopup-${status}" ${this.demo ? `style="padding-top: 25px;"` : ''}>
                    ${this.demo ? `<div class="demoHeader">${this.$i18n.t('general.head.wallet_demo')}</div>` : ''}
                    <div class="multiplier">${status === 'lose' && game.multiplier >= 1 ? (0).toFixed(2) : game.multiplier.toFixed(2)}x</div>
                    <div class="divider"></div>
                    <div class="profit">${game.profit.toFixed(game.currency.startsWith('local_') ? 2 : 8)} <icon :icon="fad fa-gem"></div>
                </div>
            `);
            $('.game-content').append(resultPopup);
            resultPopup.hide().fadeIn('fast');

            setTimeout(function() {
                resultPopup.fadeOut('fast', function() {
                    $(this).remove();
                });
            }, 2500);
        },
        random(min, max, floor = true) {
            let r = Math.random() * max + min;
            return floor ? Math.floor(r) : r;
        },
        setCookie(cname, cvalue, exdays) {
            let d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            let expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        },
        getCookie(cname) {
            let name = cname + "=", decodedCookie = decodeURIComponent(document.cookie), ca = decodedCookie.split(';');
            for(let i = 0; i <ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1);
                if (c.indexOf(name) === 0) return c.substring(name.length, c.length);
            }
            return "";
        },
        isOverview(overviewData) {
            return overviewData != null;
        },
        abbreviate(value) {
            const length = value.toFixed(0).length,
                index = Math.ceil((length - 3) / 3),
                suffix = ['k', 'm', 'g', 't'];

            if (length < 4) return value.toFixed(4).replace(/0{1,2}$/, '');

            return (value / Math.pow(1000, index))
                .toFixed(1)
                .replace(/\.0$/, '') + suffix[index - 1];
        },
        updateGameInstance(tap) {
            let game = this.gameInstance;
            tap(game);
            this.$store.commit('setGameInstance', game);
        },
        isExtendedGameStarted() {
            return this.gameInstance.game.extendedState === 'in-progress';
        },
        finishExtended(sendServerRequest = true, requestCallback = null) {
            const changeState = (data) => {
                this.updateGameInstance((i) => i.playTimeout = false);
                if(data && data.game.status !== 'cancelled') this.pushStats(data.game);
                this.updateGameInstance((i) => i.game.extendedState = 'finished');

                window.$gameRef.callback(data);
            };

            if(sendServerRequest) {
                this.updateGameInstance((i) => i.playTimeout = true);

                axios.post('/api/game/finish', { id: this.extendedGameId() }).then((data) => {
					console.log(data);
                    changeState(data);
                    if(requestCallback) requestCallback(data);
                }, () => {
                    if(requestCallback) requestCallback({ game: { status: 'lose' } });
                    changeState(null);
                });
            } else changeState(null);
        },
        pushStats(game) {
            Bus.$emit('profitMonitoring:pushStats', game);
        },
        extendedGameId() {
            return this.gameInstance.game.extendedId;
        }
    }
});
