<template>
    <div :class="'sidebar ' + (sidebar ? 'visible' : 'hidden')">
        <div class="fixed">
		
			<div class="main-header">
				{{ $t('general.head.person') }}
			</div>
			
            <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }" class="games">
               
				<router-link tag="div" to="/game/category/favorite" class="game">
                    <icon icon="fal fa-star"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.favorite') }}</div>
                </router-link>

                <router-link tag="div" to="/game/category/recent" class="game">
                    <icon icon="fal fa-history"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.recent') }}</div>
                </router-link>
				
				<router-link tag="div" to="/game/subcategory/popular" class="game">
                    <icon icon="fad fa-fire"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.popular') }}</div>
                </router-link>
				
				<router-link tag="div" to="/game/subcategory/new" class="game">
                    <icon icon="fas fa-bahai"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.new') }}</div>
                </router-link>

                <div onclick="window.open(window.location.origin + '/admin')" v-if="!isGuest && user.user.access === 'admin'" class="game">
                    <i class="fas fa-cog"></i>
                    <div class="letter-spacing">{{ $t('general.sidebar.admin') }}</div>
                </div>
				
				<div class="main-header">
					{{ $t('general.head.games') }}
				</div>
			
				<router-link tag="div" to="/game/category/inhouse" class="game">
                    <icon icon="fas fa-acorn"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.inhouse') }}</div>
                </router-link>
				
                <router-link tag="div" to="/game/category/slots" class="game">
                    <icon icon="fak fa-777"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.slots') }}</div>
                </router-link>
				
				<router-link tag="div" to="/game/subcategory/tablegames" class="game">
                    <icon icon="fas fa-table"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.tablegames') }}</div>
                </router-link>
				
				<router-link tag="div" to="/game/namecategory/roulette" class="game">
                    <icon icon="far fa-dot-circle"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.roulette') }}</div>
                </router-link>
				
				<router-link tag="div" to="/game/category/live" class="game">
                    <icon icon="fak fa-blackjack"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.live') }}</div>
                </router-link>
				
				<router-link tag="div" to="/game/category/virtualsports" class="game">
                    <icon icon="fak fa-virtualsport-pingpong"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.virtualsports') }}</div>
                </router-link>

                <router-link tag="div" to="/game/subcategory/bonus" class="game">
                    <icon icon="fad fa-box-full"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.bonus') }}</div>
                </router-link>
				
				<router-link tag="div" to="/game/category/scratch-cards" class="game">
                    <icon icon="fad fa-sparkles"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.scratchcards') }}</div>
                </router-link>

                <router-link tag="div" to="/game/subcategory/gameshows" class="game">
                    <icon icon="fad fa-star-shooting"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.gameshows') }}</div>
                </router-link>
				<router-link tag="div" to="/providers" class="game">
                    <icon icon="fas fa-gamepad"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.providers') }}</div>
                </router-link>
				
				<router-link tag="div" to="/browse" class="game">
                    <icon icon="fas fa-th-large"></icon>
                    <div class="letter-spacing">{{ $t('general.sidebar.all') }}</div>
                </router-link>

                <div class="divider"></div>

                <div class="recentTpGames">
                    <loader v-if="!lastGames"></loader>
                    <template v-else>
                        <div class="recentTpGame" v-for="game in lastGames" :key="game.game._id">
                            <div class="info">
                                <router-link tag="div" :to="`/game/${game.metadata.id}`" class="image">
                                    <icon :icon="game.metadata.icon"></icon>
                                </router-link>
                                <div class="meta">
                                    <router-link :to="`/profile/${game.game.user}`" class="player">{{ game.user.name }}</router-link>
                                    <div class="currency"><icon :icon="currencies[game.game.currency].icon" :style="{ color: currencies[game.game.currency].style }"></icon> <span style="margin-right:2px;" v-if="usd">$</span><unit :to="game.game.currency" :value="game.game.profit">$</unit></div>
                                    <router-link :to="`/game/${game.metadata.id}`" class="gameName">{{ game.metadata.name }}</router-link>
                                </div>
                            </div>
                            <router-link tag="div" :to="`/game/${game.metadata.id}`" class="btn btn-primary">{{ $t('general.play_now') }}</router-link>
                        </div>
                    </template>
                </div>
            </overlay-scrollbars>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
import AuthModal from "../modals/AuthModal";
    import Bus from "../../bus";

    export default {
        computed: {
            ...mapGetters(['isGuest', 'user', 'theme', 'games', 'currencies', 'sidebar', 'usd'])
        },
        data() {
            return {
                lastGames: null,
                maxEntries: 5
            }
        },
        created() {
            this.getGames();

            Bus.$on('event:liveGame', (e) => setTimeout(() => this.lastGames.unshift(e), e.delay));
        },
        watch: {
            lastGames() {
                if(this.lastGames && this.lastGames.length >= this.maxEntries) this.lastGames.pop();
            }
        },
        methods: {
            openAuthModal(type) {
                AuthModal.methods.open(type);
            },
            getGames() {
                this.lastGames = null;
                axios.post('/api/data/latestGames', {
                    type: 'all',
                    count: this.maxEntries
                }).then(({ data }) => this.lastGames = data.reverse());
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .sidebar.visible {
        width: 180px;

        .recentTpGames {
            display: flex !important;
        }

        .fixed {
            width: 180px;
			
			.main-header {
				padding: 15px;
				font-size: 12px;
				color: rgba(255, 255, 255, 0.7);
				font-weight: 600;
				white-space: nowrap;
				text-transform: uppercase;
				display: block !important;
				@include themed() {
					border-bottom: 2px solid t('border');
					border-top: 2px solid t('border');
				}
			}

            .game.router-link-exact-active {
                &:before {
                    background: rgba(black, .2);
                }
            }

            .game {
                justify-content: unset;
                padding-left: 17px;
                padding-right: 17px;
                position: relative;

                i {
                    width: 25px;
                }

                svg {
                    margin-right: 11px;
                }

                div {
                    display: block;
                    opacity: 1;
                }
            }
        }
    }

    .sidebar.visible ~.pageWrapper {
        padding-left: 180px;
    }

    .letter-spacing {
        letter-spacing: 0.3px;
    }

    .sidebar {
        width: $sidebar-width;
        flex-shrink: 0;
        z-index: 38002;
        transition: width 0.3s ease;

        .fixed {
            position: fixed;
            top: 0;
            width: $sidebar-width;
            height: 100%;
            border-radius: 3px;
            padding: 15px 0;
			
			.main-header {
				display: none;
			}

            @include themed() {
                border-right: 2px solid t('border');
                background: rgba(t('sidebar'), .8);
                backdrop-filter: blur(20px);
                transition: background 0.15s ease, width .3s ease;

                .games {
                    height: calc(100% - 35px);
                    //height: 100%;
                    border-radius: 3px;

                    .divider {
                        margin-top: 10px !important;
                        margin-bottom: 10px !important;
                    }

                    .recentTpGames {
                        display: none;
                        width: 100%;
                        flex-direction: column;
                        margin-top: 10px;
                        border-top: 2px solid t('border');
                        padding-top: 15px;

                        .loaderContainer {
                            width: 100%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            transform: scale(0.6);
                        }

                        .btn {
                            text-transform: uppercase;
                            margin-top: 10px;
                        }

                        .recentTpGame {
                            display: flex;
                            flex-direction: column;

                            .info {
                                display: flex;
                                padding-left: 15px;
                                padding-right: 15px;

                                .image {
                                    width: 15px;
                                    height: 15px;
                                    background-position: center;
                                    background-size: cover;
                                    margin-right: 12px;
                                    margin-top: 10px;
                                    border-radius: 3px;
                                    cursor: pointer;
                                    display: flex;
                                    opacity: .9;

                                    svg, i {
                                        margin: auto;
                                        font-size: 1em;
                                        color: t('text');
                                        opacity: .9;
                                    }
                                }

                                .meta {
                                    width: calc(100% - 50px);
                                    font-size: 0.75em;
                                    font-weight: 500;

                                    .gameName {
                                        text-transform: uppercase;
                                    }
                                }
                            }
                        }
                    }

                    .btn {
                        width: calc(100% - 30px);
                        margin-left: 15px;
                        margin-right: 15px;
                        margin-bottom: 15px;
                        border-radius: 20px;
                        font-size: 0.8em;
                        &.btn-primary {
                            border-bottom: 3px solid darken(t('secondary'), 15%);
                        }

                        &.btn-secondary {
                            border-bottom: 3px solid darken($gray-600, 15%);
                        }
                    }
                }
            }

            .game {
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0.5;
                transition: opacity 0.3s ease;
                width: 100%;
                height: 40px;
                font-size: 14px;
                cursor: pointer;
                position: relative;

                &:before {
                    transition: background .3s ease;
                    content: '';
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                }

                div {
                    display: none;
                    opacity: 0;
                    transition: opacity 1s ease;
                }

                .vue-content-placeholders-img {
                    height: 15px;
                    width: 15px;
                    border-radius: 3px;
                }

                img {
                    width: 14px;
                    height: 14px;
                }

                i {
                    cursor: pointer;
                }

                &:hover {
                    opacity: 1;
                }

                .online {
                    position: absolute !important;
                    top: 4px !important;
                    left: 17px !important;
                    border-radius: 50%;
                    width: 15px;
                    @include themed() {
                        background: t('secondary');
                    }
                    color: white;
                    height: 15px;
                    font-size: 0.5em;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                }
            }

            .game.router-link-exact-active {
				@include themed() {
					box-shadow: inset 3px 0 0 0 t('secondary');
				}
                opacity: 1;
            }
        }
    }

    @include media-breakpoint-down(md) {
        .sidebar {
            display: none;
        }
    }
</style>
