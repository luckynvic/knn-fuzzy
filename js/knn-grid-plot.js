function get_cartesian_plot(layer, x, y) {
var cartesian = [];

//fill array key

// if(layer=='grid') {
	// x = 1
	cartesian[1] = [];
	cartesian[1][1] = {x:59, y:346};

	//x = 2
	cartesian[2] = [];
	cartesian[2][1] = {x:168, y:346};
	cartesian[2][2] = {x:168, y:278};
	cartesian[2][3] = {x:168, y:214};
	cartesian[2][4] = {x:168, y:149};

	//x=3
	cartesian[3] = [];
	cartesian[3][1] = {x:280, y:346};
	cartesian[3][2] = {x:280, y:278};
	cartesian[3][3] = {x:280, y:214};
	cartesian[3][4] = {x:280, y:149};

	cartesian[4] = [];
	cartesian[4][1] = {x:388, y:346};
	cartesian[4][2] = {x:388, y:278};
	cartesian[4][3] = {x:388, y:214};

	cartesian[5] = [];
	cartesian[5][1] = {x:500, y:346};
	cartesian[5][2] = {x:500, y:278};
	cartesian[5][3] = {x:500, y:214};

	cartesian[6] = [];
	cartesian[6][1] = {x:612, y:346};
	cartesian[6][2] = {x:612, y:278};
	cartesian[6][3] = {x:612, y:214};

	cartesian[7] = [];
	cartesian[7][1] = {x:721, y:346};
	cartesian[7][2] = {x:721, y:278};
	cartesian[7][3] = {x:721, y:214};

	cartesian[8] = [];
	cartesian[8][1] = {x:831, y:346};
	cartesian[8][2] = {x:831, y:278};
	cartesian[8][3] = {x:831, y:214};

	cartesian[9] = [];
	cartesian[9][1] = {x:942, y:346};
	cartesian[9][2] = {x:942, y:278};
	cartesian[9][3] = {x:942, y:214};

// }

// if(layer=='map')
// {
// cartesian[1][1] = {x:59, y:346};
// cartesian[9][1] = {x:941, y:346};	
// }

result = cartesian[x][y];
return result
}