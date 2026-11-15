import ProfileController from './ProfileController'
import LeaderboardController from './LeaderboardController'

const Api = {
    ProfileController: Object.assign(ProfileController, ProfileController),
    LeaderboardController: Object.assign(LeaderboardController, LeaderboardController),
}

export default Api