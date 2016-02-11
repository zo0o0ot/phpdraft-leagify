<?php
namespace PhpDraft\Domain\Repositories;

use Silex\Application;
use PhpDraft\Domain\Entities\Trade;
use PhpDraft\Domain\Entities\TradeAsset;

class DraftDataRepository {
  private $app;

  private $colors;
  private $position_colors;
  private $sports;
  private $styles;
  private $statuses;

  private $mlb_teams;
  private $nba_teams;
  private $nfl_teams;
  private $nhl_teams;
  private $alma_mater_teams;

  private $mlb_positions;
  private $nba_positions;
  private $nfl_positions;
  private $nhl_positions;
  private $extended_nfl_positions;
  private $super_rugby_positions;
  private $alma_mater_positions;

  public function __construct(Application $app) {
    $this->app = $app;

    $this->colors = array(
        "light_orange" => "#FFCC66",
        "light_blue" => "#CCFFFF",
        "light_yellow" => "#FFFF99",
        "light_red" => "#FF9999",
        "dark_green" => "#99CC99",
        "light_purple" => "#CCCCFF",
        "light_green" => "#CCFFCC",
        "light_pink" => "#FFCCFF",
        "seafoam" => "#99CCCC",
        "switchgrass" => "#DAFF7F",
        "gray" => "#99CCCC"
    );

    $this->position_colors = array(
        //Hockey/NHL
        "LW" => $this->colors['light_orange'], //LT ORANGE    *
        "C" => $this->colors['light_blue'], //LT BLUE   *
        "RW" => $this->colors['light_yellow'], //LT YELLOW  *
        "D" => $this->colors['light_red'], //LT RED   *
        "G" => $this->colors['dark_green'], //DK GREEN    *
        //Football/NFL
        "QB" => $this->colors['light_blue'], //LT BLUE
        "RB" => $this->colors['light_orange'], //LT ORANGE
        "WR" => $this->colors['light_yellow'], //LT YELLOW
        "TE" => $this->colors['light_red'], //LT RED
        "DEF" => $this->colors['dark_green'], //DK GREEN
        "K" => $this->colors['light_purple'], //LT PURPLE   *
        "DL" => $this->colors['light_green'],
        "LB" => $this->colors['light_pink'],
        "DB" => $this->colors['seafoam'],
        "OL" => $this->colors['switchgrass'],
        //Baseball/MLB
        //CATCHER ALREADY DONE: LT BLUE
        "1B" => $this->colors['light_orange'], //LT ORANGE
        "2B" => $this->colors['light_yellow'], //LT YELLOW
        "3B" => $this->colors['light_red'], //LT RED
        "SS" => $this->colors['dark_green'], //DK GREEN
        "OF" => $this->colors['light_green'], //LT GREEN    *
        "UTIL" => $this->colors['light_pink'], //LT PINK    *
        "SP" => $this->colors['light_purple'], //LT PURPLE
        "RP" => $this->colors['seafoam'], //SEAFOAM       *
        //Basketball/NBA
        "PG" => $this->colors['light_orange'], //LT ORANGE
        "SG" => $this->colors['dark_green'], //DK GREEN
        "SF" => $this->colors['light_yellow'], //LT YELLOW
        "PF" => $this->colors['light_red'], //LT RED
        //CENTER ALREADY DONE: LT BLUE
        //Rugby/Super Rugby
        "FR" => $this->colors['light_orange'],
        "HB" => $this->colors['light_yellow'],
        "LF" => $this->colors['light_red'],
        "OB" => $this->colors['dark_green'],
        "M" => $this->colors['light_green'],
        "L" => $this->colors['light_pink'],
        "FH" => $this->colors['light_purple'],
        //Alma Mater League
        "ACC" => $this->colors['light_blue'], //LT BLUE
        "B12" => $this->colors['light_orange'], //LT ORANGE
        "B10" => $this->colors['light_yellow'], //LT YELLOW
        "P12" => $this->colors['light_red'], //LT RED
        "SEC" => $this->colors['dark_green'], //DK GREEN
        "FBS" => $this->colors['light_purple'], //LT PURPLE   *
        "FCS" => $this->colors['light_green'],
        "D2" => $this->colors['light_pink'],
        "D3" => $this->colors['seafoam']
    );

    $this->sports = array(
      "NFL" => "Football (NFL)",
      "NFLE" => "Football - Extended Rosters (NFL)",
      "MLB" => "Baseball (MLB)",
      "NBA" => "Basketball (NBA)",
      "NHL" => "Hockey (NHL)",
      "S15" => "Rugby (Super 15)",
      "AMD" => "NFL Draft (Alma Mater Draft)"
    );

    $this->styles = array(
      "serpentine" => "Serpentine Draft",
      "standard" => "Standard Draft"
    );

    $this->statuses = array(
      "undrafted" => "Setting Up",
      "in_progress" => "Currently Drafting",
      "complete" => "Draft Complete"
    );
    
    //PHPD-70: FIX 3 INFIELD ISSUES WITH CSS CLASS NAMES:
    $this->position_colors = array_merge($this->position_colors, array(
      "x1B" => $this->position_colors['1B'],
      "x2B" => $this->position_colors['2B'],
      "x3B" => $this->position_colors['3B'],
    ));
    
    $this->nhl_teams = array(
        "ANA" => "Anaheim Ducks",
        "BOS" => "Boston Bruins",
        "BUF" => "Buffalo Sabres",
        "CGY" => "Calgary Flames",
        "CAR" => "Carolina Hurricanes",
        "CHI" => "Chicago Blackhawks",
        "COL" => "Colorado Avalanche",
        "CBS" => "Columbus Blue Jackets",
        "DAL" => "Dallas Stars",
        "DET" => "Detroit Red Wings",
        "EDM" => "Edmonton Oilers",
        "FLA" => "Florida Panthers",
        "LAK" => "Los Angeles Kings",
        "MIN" => "Minnesota Wild",
        "MTL" => "Montreal Canadiens",
        "NSH" => "Nashville Predators",
        "NJD" => "New Jersey Devils",
        "NYI" => "New York Islanders",
        "NYR" => "New York Rangers",
        "OTT" => "Ottawa Senators",
        "PHI" => "Philadelphia Flyers",
        "ARI" => "Arizona Coyotes",
        "PIT" => "Pittsburgh Penguins",
        "SJS" => "San Jose Sharks",
        "STL" => "St Louis Blues",
        "TAM" => "Tampa Bay Lightning",
        "TOR" => "Toronto Maple Leafs",
        "VAN" => "Vancouver Canucks",
        "WAS" => "Washington Capitals",
        "WPG" => "Winnipeg Jets"
    );

    $this->nhl_positions = array(
        "LW" => "Left Wing",
        "RW" => "Right Wing",
        "C" => "Center",
        "D" => "Defenseman",
        "G" => "Goaltender"
    );

    $this->nfl_teams = array(
        "ARI" => "Arizona Cardinals",
        "ATL" => "Atlanta Falcons",
        "BAL" => "Baltimore Ravens",
        "BUF" => "Buffalo Bills",
        "CAR" => "Carolina Panthers",
        "CHI" => "Chicago Bears",
        "CIN" => "Cincinnati Bengals",
        "CLE" => "Cleveland Browns",
        "DAL" => "Dallas Cowboys",
        "DEN" => "Denver Broncos",
        "DET" => "Detroit Lions",
        "GNB" => "Green Bay Packers",
        "HOU" => "Houston Texans",
        "IND" => "Indianapolis Colts",
        "JAC" => "Jacksonville Jaguars",
        "K.C" => "Kansas City Chiefs",
        "MIA" => "Miami Dolphins",
        "MIN" => "Minnesota Vikings",
        "NWE" => "New England Patriots",
        "NOR" => "New Orleans Saints",
        "NYG" => "New York Giants",
        "NYJ" => "New York Jets",
        "OAK" => "Oakland Raiders",
        "PHI" => "Philadelphia Eagles",
        "PIT" => "Pittsburgh Steelers",
        "SDG" => "San Diego Chargers",
        "SFO" => "San Francisco 49ers",
        "SEA" => "Seattle Seahawks",
        "STL" => "St. Louis Rams",
        "TAM" => "Tampa Bay Buccaneers",
        "TEN" => "Tennessee Titans",
        "WAS" => "Washington Redskins"
    );

    $this->nfl_positions = array(
        "QB" => "Quarterback",
        "RB" => "Runningback",
        "WR" => "Wide Receiver",
        "TE" => "Tight End",
        "DEF" => "Defense",
        "K" => "Kicker"
    );

    $this->extended_nfl_positions = array_merge($this->nfl_positions, array(
      "DL" => "Defensive Lineman",
      "LB" => "Linebacker",
      "DB" => "Defensive Back",
      "OL" => "Offensive Lineman"
    ));

    $this->mlb_teams = array(
        "ARI" => "Arizona Diamondbacks",
        "ATL" => "Atlanta Braves",
        "BAL" => "Baltimore Orioles",
        "BOS" => "Boston Red Sox",
        "CHC" => "Chicago Cubs",
        "CWS" => "Chicago White Sox",
        "CIN" => "Cincinnati Reds",
        "CLE" => "Cleveland Indians",
        "COL" => "Colorado Rockies",
        "DET" => "Detroit Tigers",
        "HOU" => "Houston Astros",
        "K.C" => "Kansas City Royals",
        "LAA" => "Los Angeles Angels",
        "LAD" => "Los Angeles Dodgers",
        "MIA" => "Miami Marlins",
        "MIL" => "Milwaukee Brewers",
        "MIN" => "Minnesota Twins",
        "NYM" => "New York Mets",
        "NYY" => "New York Yankees",
        "OAK" => "Oakland Athletics",
        "PHI" => "Philadelphia Phillies",
        "PIT" => "Pittsburgh Pirates",
        "SDG" => "San Diego Padres",
        "SFO" => "San Francisco Giants",
        "SEA" => "Seattle Mariners",
        "STL" => "St Louis Cardinals",
        "TAM" => "Tampa Bay Rays",
        "TEX" => "Texas Rangers",
        "TOR" => "Toronto Blue Jays",
        "WAS" => "Washington Nationals"
    );

    $this->mlb_positions = array(
        "C" => "Catcher",
        "1B" => "1st Base",
        "2B" => "2nd Base",
        "3B" => "3rd Base",
        "SS" => "Shortstop",
        "OF" => "Outfielder",
        "UTIL" => "Utility",
        "SP" => "Starting Pitcher",
        "RP" => "Relief Pitcher"
    );

    $this->nba_teams = array(
        "ATL" => "Atlanta Hawks",
        "BKN" => "Brooklyn Nets",
        "BOS" => "Boston Celtics",
        "CHI" => "Chicago Bulls",
        "CHA" => "Charlotte Hornets",
        "CLE" => "Cleveland Cavaliers",
        "DAL" => "Dallas Mavericks",
        "DEN" => "Denver Nuggets",
        "DET" => "Detroit Pistons",
        "GSW" => "Golden State Warriors",
        "HOU" => "Houston Rockets",
        "IND" => "Indiana Pacers",
        "LAC" => "Los Angeles Clippers",
        "LAL" => "Los Angeles Lakers",
        "MIL" => "Milwaukee Bucks",
        "NYK" => "New York Knicks",
        "PHI" => "Philadelphia 76ers",
        "PHO" => "Phoenix Suns",
        "POR" => "Portland Trail Blazers",
        "SAC" => "Sacramento Kings",
        "SAS" => "San Antonio Spurs",
        "OKC" => "Oklahoma City Thunder",
        "UTH" => "Utah Jazz",
        "WAS" => "Washington Wizards",
        "NOR" => "New Orleans Pelicans",
        "MIA" => "Miami Heat",
        "MIN" => "Minnesota Timberwolves",
        "ORL" => "Orlando Magic",
        "TOR" => "Toronto Raptors",
        "MEM" => "Memphis Grizzlies"
    );

    $this->nba_positions = array(
        "PG" => "Point Guard",
        "SG" => "Shooting Guard",
        "SF" => "Small Forward",
        "PF" => "Power Forward",
        "C" => "Center"
    );

    $this->super_rugby_positions = array(
        "FH" => "Fly Half",
        "OB" => "Outside Back",
        "M"  => "Midfielder",
        "LF" => "Loose Forward",
        "HB" => "Half Back",
        "L"  => "Lock",
        "FR" => "Front Row"
    );

    $this->super_rugby_teams = array(
        "BLU" => "Blues",
        "BRU" => "Brumbies",
        "BUL" => "Bulls",
        "CHE" => "Cheetahs",
        "CHI" => "Chiefs",
        "CRU" => "Crusaders",
        "FOR" => "Force",
        "HIG" => "Highlanders",
        "HUR" => "Hurricanes",
        "JAG" => "Jaguars",
        "KIN" => "Kings",
        "LIO" => "Lions",
        "REB" => "Rebels",
        "RED" => "Reds",
        "SHA" => "Sharks",
        "STO" => "Stormers",
        "SUN" => "Sunwolves",
        "WAR" => "Waratahs"
    );
    
    $this->alma_mater_positions = array(
        "ACC" => "Atlantic Coast Conference",
        "B12" => "Big 12",
        "B10"  => "Big 10",
        "P12" => "Pac 12",
        "SEC" => "Southeastern Conference",
        "FBS"  => "FBS - Not in Power 5",
        "FCS" => "Football Championship Subdivision",
        "D2" => "Division 2",
        "D3" => "Division 3",
    );

    $this->alma_mater_teams = array(
        "FBS - AAC" => "FBS - AAC",
        "ACC" => "ACC",
        "Big 12" => "Big 12",
        "Big Ten" => "Big Ten",
        "Pac 12" => "Pac 12",
        "SEC" => "SEC",
        "FBS - Mountain West" => "FBS - Mountain West",
        "FBS - Independent" => "FBS - Independent",
        "FBS - CUSA" => "FBS - CUSA",
        "FBS - MAC" => "FBS - MAC",
        "FBS - Sun Belt" => "FBS - Sun Belt",
        "FCS" => "FCS",
        "D2" => "Division 2",
        "D3" => "Division 3",
        "2016" => "2016",
        "CFB" => "College Football"
    );
  }

  public function GetPositionColors() {
    return $this->position_colors;
  }

  public function GetSports() {
    return $this->sports;
  }

  public function GetStyles() {
    return $this->styles;
  }

  public function GetStatuses() {
    return $this->statuses;
  }

  public function GetTeams($pro_league) {
    switch (strtolower($pro_league)) {
      case 'nhl':
      case'hockey':
        return $this->nhl_teams;
        break;
      case 'nfl':
      case 'football':
        return $this->nfl_teams;
        break;
      case 'mlb':
      case 'baseball':
        return $this->mlb_teams;
        break;
      case 'nba':
      case 'basketball':
        return $this->nba_teams;
        break;
      case 's15':
        return $this->super_rugby_teams;
        break;
      case 'amd':
        return $this->alma_mater_teams;
        break;
    }
  }

  public function GetPositions($pro_league) {
    switch (strtolower($pro_league)) {
      case 'nhl':
      case 'hockey':
        return $this->nhl_positions;
        break;
      case 'nfl':
      case 'football':
        return $this->nfl_positions;
        break;
      case 'nfle':
        return $this->extended_nfl_positions;
        break;
      case 'mlb':
      case 'baseball':
        return $this->mlb_positions;
        break;
      case 'nba':
      case 'basketball':
        return $this->nba_positions;
        break;
      case 's15':
        return $this->super_rugby_positions;
        break;
      case 'amd':
        return $this->alma_mater_positions;
        break;
    }
  }
}