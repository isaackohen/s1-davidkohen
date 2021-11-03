<template>
    <div class="gameCategory">
		<template v-if="!gamesLoading">
        <div class="header">
            {{ $t('general.sidebar.' + $route.params.category) }}
        </div>
			<div class="warning" v-if="Object.keys(categoryGames).length === 0">
				{{ $t('general.sidebar.no_games') }}
			</div>
			<games :categories="categoryGames"></games>
		</template>
		<div v-else class="games-load">
			<loader></loader>
		</div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        data() {
            return {
                categoryGames: {},
				gamesData: {},
				gamesLoading: true,
				page: 0
            }
        },
		watch: {
            gamesData() {
                this.categoryGames = {};
                this.load();
            }
        },
        computed: {
            ...mapGetters(['isGuest'])
        },
        created() {
			var payload = {
				subcategory: [
					this.$route.params.category
				],
				page: this.page
			};
			axios.post('/api/data/games', payload).then(({ data }) => {
                this.gamesData = data[0].games;
				if(this.gamesData.length === 0) {
					this.$router.push('/404');
					return;
				}
				this.gamesLoading = false;
            });
			this.load();
        },
		methods: {
            load() {
				let duplicates = [];
				_.forEach(this.gamesData, (game) => {
					if (game.cat.includes(this.$route.params.category)) {
						if (duplicates.includes(game.id)) return;
						duplicates.push(game.id);

						if (!this.categoryGames[game.cat]) this.categoryGames[game.cat] = [game];
						else this.categoryGames[game.cat].push(game);
					}
				});
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import "resources/sass/variables";

	.games-load .loader {
		margin: auto;
	}
	
    .warning {
        width: 100%;
        text-align: center;
        font-size: 1.1em;
        margin-top: 15px;
        margin-bottom: 15px;

     }

    .gameCategory {
        @include themed() {
            .header {
                background: rgba(t('sidebar'), .8);
                backdrop-filter: blur(20px);
                border-bottom: 2px solid t('border');
                margin-top: -15px;
                padding: 35px 45px;
                font-size: 1.8em;
                position: sticky;
                top: 73px;
                z-index: 555;
            }
        }
    }
</style>
